<?php

namespace Bankiru\Api\DependencyInjection\Compiler;

use ScayTrase\Api\Rpc\Decorators\LoggableRpcClient;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Reference;

class LoggerDecoratorPass implements CompilerPassInterface
{
    const LOGGER_SERVICE_PARAMETER = 'bankiru_api.logger.service';

    /**
     * {@inheritdoc}
     * @throws InvalidArgumentException
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('logger')) {
            return;
        }

        if (false === $container->getParameter(self::LOGGER_SERVICE_PARAMETER)) {
            return;
        }

        $clients = $container->findTaggedServiceIds('rpc_client');

        foreach ($clients as $id => $tags) {
            $newId = $id . '.loggable';
            $container->register($newId, LoggableRpcClient::class)
                      ->setArguments(
                          [
                              new Reference($newId . '.inner'),
                              new Reference($container->getParameter(self::LOGGER_SERVICE_PARAMETER)),
                              '%bankiru_api.logger.debug_body%'
                          ]
                      )
                      ->setPublic(false)
                      ->setDecoratedService($id);
        }
    }
}
