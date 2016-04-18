<?php
/**
 * Created by PhpStorm.
 * User: batanov.pavel
 * Date: 21.03.2016
 * Time: 12:25
 */

namespace Bankiru\Api\DependencyInjection\Compiler;

use Bankiru\Api\Client\Profiling\ProfiledClient;
use Bankiru\Api\Client\Profiling\TraceableClient;
use Bankiru\Api\DataCollector\RpcProfiler;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Reference;

class ProfilerDecoratorPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     * @throws InvalidArgumentException
     */
    public function process(ContainerBuilder $container)
    {
        if (false === $container->getParameter('bankiru_api.profiler_enabled')) {
            return;
        }

        $clients = $container->findTaggedServiceIds('rpc_client');

        $collector = $container->getDefinition('bankiru_api.profiler.collector');

        foreach ($clients as $id => $tags) {

            $clientName = 'api_client';
            foreach ($tags as $tag => $attributes) {
                $clientName = $attributes['client_name'];
            }

            $profiler   = new Definition(RpcProfiler::class, [$clientName]);
            $profilerId = $id . '_profiler';
            $container->setDefinition($profilerId, $profiler);

            $profiledId = $id . '.profiled';
            $container->register($profiledId, ProfiledClient::class)
                      ->setArguments(
                          [
                              new Reference($profiledId . '.inner'),
                              new Reference($profilerId),
                          ]
                      )
                      ->setPublic(false)
                      ->setDecoratedService($id, null, 255);

            if ($container->has('debug.stopwatch')) {
                $tracedId = $id . '.traced';
                $container->register($tracedId, TraceableClient::class)
                          ->setArguments(
                              [
                                  new Reference($tracedId . '.inner'),
                                  new Reference('debug.stopwatch'),
                                  'rpc_client.' . $clientName,
                              ]
                          )
                          ->setPublic(false)
                          ->setDecoratedService($id, null, -255);

            }

            $collector->addMethodCall('addProfiler', [new Reference($profilerId)]);
        }
    }
}
