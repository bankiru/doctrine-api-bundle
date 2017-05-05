<?php

namespace Bankiru\Api\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ApiFactoryPass implements CompilerPassInterface
{
    /** {@inheritdoc} */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('bankiru_api.api_factory.chain')) {
            return;
        }

        $chain = $container->getDefinition('bankiru_api.api_factory.chain');

        $factories = $container->findTaggedServiceIds('bankiru.api_factory');

        foreach ($factories as $id => $tags) {
            $chain->addMethodCall('add', [new Reference($id)]);
        }
    }
}
