<?php

namespace Bankiru\Api\Tests;

use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\DependencyInjection\MergeExtensionConfigurationPass;

abstract class ContainerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param BundleInterface[] $bundles
     * @param array             $configs
     * @param bool              $compile
     *
     * @return ContainerBuilder
     */
    protected function buildContainer(array $bundles = [], array $configs = [], $compile = true)
    {
        $container = new ContainerBuilder(
            new ParameterBag(
                [
                    'kernel.debug'       => false,
                    'kernel.bundles'     => array_map('get_class', $bundles),
                    'kernel.cache_dir'   => CACHE_DIR . 'test',
                    'kernel.environment' => 'test',
                    'kernel.root_dir'    => __DIR__,
                ]
            )
        );
        $container->set('annotation_reader', new AnnotationReader());

        $container->addObjectResource($container);
        $extensions = [];
        foreach ($bundles as $bundle) {
            if ($extension = $bundle->getContainerExtension()) {
                $container->registerExtension($extension);
                $extensions[] = $extension->getAlias();
            }

            $container->addObjectResource($bundle);
        }

        foreach ($bundles as $bundle) {
            $bundle->build($container);
        }

        foreach ($configs as $alias => $config) {
            $container->prependExtensionConfig($alias, $config);
        }

        // ensure these extensions are implicitly loaded
        $container->getCompilerPassConfig()->setMergePass(new MergeExtensionConfigurationPass($extensions));

        foreach ($bundles as $bundle) {
            $bundle->setContainer($container);
            $bundle->boot();
        }

        $container->compile();
        $dumper = new PhpDumper($container);
        file_put_contents(CACHE_DIR . '/container.php', $dumper->dump());

        return $container;
    }
}
