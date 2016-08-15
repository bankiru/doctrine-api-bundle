<?php

namespace Bankiru\Api\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;

class ApiExtension extends Extension
{
    /**
     * Loads a specific configuration.
     *
     * @param array            $configs   An array of configuration values
     * @param ContainerBuilder $container A ContainerBuilder instance
     *
     * @throws \InvalidArgumentException When provided tag is not defined in this extension
     */
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

        $container->setParameter('bankiru_api.logger_id', $config['logger']['id']);
        $container->setParameter('bankiru_api.profiler_enabled', $config['profiling']);

        $configuration = $container->getDefinition('bankiru_api.configuration');
        if ($config['cache']['enabled']) {
            if (null === $config['cache']['service']) {
                throw new \LogicException('You should specify PSR-6 cache service in order to enable caching');
            }

            $configuration->addMethodCall('setApiCache', [new Reference($config['cache']['service'])]);
            if ($config['cache']['logger'] !== null) {
                $configuration->addMethodCall('setApiCacheLogger', [new Reference($config['cache']['logger'])]);
            }
        }

        foreach ($config['cache']['configuration'] as $class => $options) {
            assert(array_key_exists('enabled', $options));
            assert(array_key_exists('ttl', $options));
            $configuration->addMethodCall('setCacheConfiguration', [$class, $options]);
        }
    }

    public function getAlias()
    {
        return 'api_client';
    }
}
