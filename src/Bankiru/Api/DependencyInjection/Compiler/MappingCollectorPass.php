<?php

namespace Bankiru\Api\DependencyInjection\Compiler;

use Bankiru\Api\Doctrine\Mapping\Driver\YmlMetadataDriver;
use Doctrine\Common\Persistence\Mapping\Driver\SymfonyFileLocator;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

class MappingCollectorPass implements CompilerPassInterface
{

    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('bankiru_api.chain_driver')) {
            return;
        }

        $driver = $container->getDefinition('bankiru_api.chain_driver');

        foreach ($container->getParameter('kernel.bundles') as $bundle) {
            /** @var BundleInterface $bundle */
            $refl      = new \ReflectionClass($bundle);
            $path      = dirname($refl->getFileName()) . '/Resources/config/api/';
            $namespace = $refl->getNamespaceName() . '\Entity';

            $locatorDef = new Definition(
                SymfonyFileLocator::class,
                [
                    [
                        $path => $namespace,
                    ],
                    '.api.yml',
                    DIRECTORY_SEPARATOR
                ]
            );
            $driverDef = new Definition(YmlMetadataDriver::class,[$locatorDef]);

            $driver->addMethodCall('addDriver', [$driverDef, $namespace]);
        }
    }
}
