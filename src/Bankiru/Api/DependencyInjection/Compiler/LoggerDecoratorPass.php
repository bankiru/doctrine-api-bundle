<?php
/**
 * Created by PhpStorm.
 * User: batanov.pavel
 * Date: 21.03.2016
 * Time: 12:25
 */

namespace Bankiru\Api\DependencyInjection\Compiler;


use ScayTrase\Api\Rpc\Decorators\LoggableRpcClient;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Reference;

class LoggerDecoratorPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     * @throws InvalidArgumentException
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('logger')) {
            return;
        }

        if (false === $container->getParameter('bankiru_api.logger_id')) {
            return;
        }

        $clients = $container->findTaggedServiceIds('rpc_client');

        foreach ($clients as $id => $tags) {
            $newId = $id . '.loggable';
            $container->register($newId, LoggableRpcClient::class)
                      ->setArguments(
                          [
                              new Reference($newId . '.inner'),
                              new Reference($container->getParameter('bankiru_api.logger_id')),
                          ]
                      )
                      ->setPublic(false)
                      ->setDecoratedService($id);
        }
    }
}
