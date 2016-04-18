<?php
/**
 * Created by PhpStorm.
 * User: batanov.pavel
 * Date: 21.03.2016
 * Time: 13:20
 */

namespace Bankiru\Api\DependencyInjection;

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
        $logger->addDefaultsIfNotSet();
        $logger->children()->scalarNode('id')->defaultValue('logger');

        $profiling = $root->children()->booleanNode('profiling')->defaultValue('%kernel.debug%');

        $cache = $root->children()->arrayNode('cache');
        $cache->addDefaultsIfNotSet();
        $cache->children()
              ->booleanNode('enabled')
              ->defaultFalse();
        $cache
            ->addDefaultsIfNotSet()
            ->treatFalseLike(['enabled' => false])
            ->treatTrueLike(['enabled' => true])
            ->treatNullLike(['enabled' => true]);
        $cache->children()->scalarNode('service')->defaultNull()->info('PSR-6 Cache service. Required if enabled');
        $cache->children()->scalarNode('logger')->defaultNull()->info('PSR-3 Log service for cache');
        $cache->beforeNormalization()->ifString()->then(function ($v) {
            return ['service' => $v, 'enabled' => true];
        });
        $configuration = $cache->children()->arrayNode('configuration');
        $configuration->useAttributeAsKey('class');
        /** @var ArrayNodeDefinition $confProto */
        $confProto = $configuration->prototype('array');
        $confProto->children()->scalarNode('ttl')->isRequired();
        $confProto->beforeNormalization()
                  ->ifTrue(function ($v) { return is_int($v); })
                  ->then(function ($v) { return ['ttl' => $v]; });
        $confProto->ignoreExtraKeys(false);
        $confProto->canBeEnabled();

        return $builder;
    }
}
