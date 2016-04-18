<?php
/**
 * Created by PhpStorm.
 * User: batanov.pavel
 * Date: 21.03.2016
 * Time: 12:18
 */

namespace Bankiru\Api\Tests;

use Bankiru\Api\ApiBundle;
use Bankiru\Api\ClientRegistryInterface;
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

        self::assertTrue($container->has('test_rpc_client'));
        self::assertInstanceOf(LoggableRpcClient::class, $container->get('test_rpc_client'));

        /** @var ClientRegistryInterface $registry */
        $registry = $container->get('bankiru_api.entity_manager')->getConfiguration()->getRegistry();
        foreach ($registry->all() as $client) {
            self::assertInstanceOf(LoggableRpcClient::class, $client);
        }
    }
}
