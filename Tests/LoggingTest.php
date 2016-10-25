<?php

namespace Bankiru\Api\Tests;

use Bankiru\Api\ApiBundle;
use Bankiru\Api\Doctrine\ClientRegistryInterface;
use ScayTrase\Api\Rpc\Decorators\LoggableRpcClient;
use Symfony\Bundle\MonologBundle\MonologBundle;

class LoggingTest extends ContainerTest
{
    public function testClientClass()
    {
        $container = $this->buildContainer(
            [
                new MonologBundle(),
                new ApiBundle(),
            ],
            [
                'api_client' => [
                    'logger'    => [
                        'id' => 'logger',
                    ],
                    'profiling' => false,
                ],
            ]
        );

        self::assertTrue($container->has('rpc.test_client'));
        self::assertInstanceOf(LoggableRpcClient::class, $container->get('rpc.test_client'));

        /** @var ClientRegistryInterface $registry */
        $registry = $container->get('bankiru_api.entity_manager')->getConfiguration()->getClientRegistry();
        foreach ($registry->all() as $client) {
            self::assertInstanceOf(LoggableRpcClient::class, $client);
        }
    }
}
