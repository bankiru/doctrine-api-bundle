<?php
/**
 * Created by PhpStorm.
 * User: batanov.pavel
 * Date: 09.02.2016
 * Time: 15:42
 */

namespace Bankiru\Api\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ClientCollectorPass implements CompilerPassInterface
{

    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('bankiru_api.client_registry')) {
            return;
        }

        $registry = $container->getDefinition('bankiru_api.client_registry');

        $clients = $container->findTaggedServiceIds('rpc_client');

        foreach ($clients as $id => $tags) {
            foreach ($tags as $tag => $attributes) {
                $registry->addMethodCall('add', [$attributes['client_name'], new Reference($id)]);
            }
        }
    }
}
