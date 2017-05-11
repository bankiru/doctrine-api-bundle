<?php

namespace Bankiru\Api\DependencyInjection;

use Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;

class BankiruDoctrineApiExtension extends Extension
{
    /** {@inheritdoc} */
    public function load(array $configs, ContainerBuilder $container)
    {
        $config = $this->processConfiguration(new Configuration(), $configs);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('api.yml');

        if ($container->hasParameter('kernel.environment') &&
            $container->getParameter('kernel.environment') === 'test'
        ) {
            $loader->load('test.yml');
        }

        if ($this->isConfigEnabled($container, $config['logger'])) {
            $container->setParameter('bankiru_api.logger.service', $config['logger']['service']);
            $container->setParameter('bankiru_api.logger.debug_body', $config['logger']['debug_body']);
        }

        $container->setParameter('bankiru_api.profiler_enabled', $config['profiling']);

        $configuration = $container->getDefinition('bankiru_api.configuration');
        if ($this->isConfigEnabled($container, $config['entity_cache'])) {
            if (null === $config['entity_cache']['service']) {
                throw new \LogicException('You should specify PSR-6 cache service in order to enable caching');
            }

            $configuration->addMethodCall('setApiCache', [new Reference($config['entity_cache']['service'])]);
            if ($config['entity_cache']['logger'] !== null) {
                $configuration->addMethodCall('setApiCacheLogger', [new Reference($config['entity_cache']['logger'])]);
            }
        }

        if ($this->isConfigEnabled($container, $config['metadata_cache'])) {
            $container->getDefinition('bankiru_api.metadata_factory')
                      ->addMethodCall('setCacheDriver', [new Reference($config['metadata_cache']['service'])]);
        }

        $this->processSensioExtraConfig($container, $loader);

        foreach ($config['entity_cache']['configuration'] as $class => $options) {
            assert(array_key_exists('enabled', $options));
            assert(array_key_exists('ttl', $options));
            $configuration->addMethodCall('setCacheConfiguration', [$class, $options]);
        }
    }

    public function getAlias()
    {
        return 'api_client';
    }

    /**
     * @param ContainerBuilder $container
     * @param YamlFileLoader   $loader
     */
    private function processSensioExtraConfig(ContainerBuilder $container, YamlFileLoader $loader)
    {
        if (!$container->hasParameter('kernel.bundles')) {
            return;
        }

        if (!class_exists('Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle')) {
            return;
        }

        if (in_array(SensioFrameworkExtraBundle::class, $container->getParameter('kernel.bundles'))) {
            $loader->load('sensio.yml');
        }
    }
}
