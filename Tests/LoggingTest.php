<?php

namespace Bankiru\Api\Tests;

use Bankiru\Api\BankiruDoctrineApiBundle;
use Bankiru\Api\Doctrine\ClientRegistryInterface;
use PHPUnit\Framework\TestCase;
use ScayTrase\Api\Rpc\Decorators\LoggableRpcClient;
use Symfony\Bundle\MonologBundle\MonologBundle;

final class LoggingTest extends TestCase
{
    use ContainerTestTrait;

    public function testClientClass()
    {
        $container = $this->buildContainer(
            [
                new MonologBundle(),
                new BankiruDoctrineApiBundle(),
            ],
            [
                'api_client' => [
                    'logger'    => [
                        'service' => 'logger',
                        'debug_body'   => true,
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

        self::assertTrue($container->getParameter('bankiru_api.logger.debug_body'));
    }

    /** {@inheritdoc} */
    protected function getCacheDir()
    {
        return __DIR__ . '/../build/cache/';
    }
}
