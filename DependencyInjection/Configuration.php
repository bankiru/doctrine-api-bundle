<?php

namespace Bankiru\Api\DependencyInjection;

use Doctrine\Common\Cache\Cache;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /** {@inheritdoc} */
    public function getConfigTreeBuilder()
    {
        $builder = new TreeBuilder();
        $root    = $builder->root('api_client');

        $logger = $root->children()->arrayNode('logger');
        $logger->canBeEnabled();
        $logger->addDefaultsIfNotSet();
        $logger->beforeNormalization()->ifString()->then(
            function ($v) {
                return ['service' => $v, 'enabled' => true];
            }
        );
        $logger->children()->booleanNode('debug_body')->defaultFalse();
        $logger->children()->scalarNode('service')->defaultValue('logger');

        $root->children()->booleanNode('profiling')->defaultValue('%kernel.debug%');

        $metadataCache = $root->children()->arrayNode('metadata_cache');
        $metadataCache->canBeEnabled();
        $metadataCache->children()->scalarNode('service')->info(
            Cache::class . ' Implementation service id. Required if enabled'
        )->defaultNull();
        $metadataCache->beforeNormalization()->ifString()->then(
            function ($v) {
                return ['service' => $v, 'enabled' => true];
            }
        );

        $cache = $root->children()->arrayNode('entity_cache');
        $cache->canBeEnabled();
        $cache->children()->scalarNode('service')->info('PSR-6 Cache service. Required if enabled');
        $cache->children()->scalarNode('logger')->defaultNull()->info('PSR-3 Log service for cache');
        $cache->beforeNormalization()->ifString()->then(
            function ($v) {
                return ['service' => $v, 'enabled' => true];
            }
        );
        $configuration = $cache->children()->arrayNode('configuration');
        $configuration->useAttributeAsKey('class');
        /** @var ArrayNodeDefinition $confProto */
        $confProto = $configuration->prototype('array');
        $confProto->children()->scalarNode('ttl')->isRequired();
        $confProto->beforeNormalization()
                  ->ifTrue(
                      function ($v) {
                          return is_int($v);
                      }
                  )
                  ->then(
                      function ($v) {
                          return ['ttl' => $v];
                      }
                  );
        $confProto->children()->variableNode('extra')
                  ->info('Extra data for entity cache configuration')
                  ->defaultValue([]);
        $confProto->ignoreExtraKeys(false);
        $confProto->canBeEnabled();

        return $builder;
    }
}
