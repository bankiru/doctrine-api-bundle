<?php

namespace Bankiru\Api\Tests;

use Bankiru\Api\BankiruDoctrineApiBundle;
use Bankiru\Api\Client\TraceableClient;
use Bankiru\Api\Doctrine\ClientRegistryInterface;
use Bankiru\Api\Doctrine\Test\RpcRequestMock;
use PHPUnit\Framework\TestCase;
use ScayTrase\Api\Rpc\RpcResponseInterface;
use Symfony\Bundle\MonologBundle\MonologBundle;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

final class StopwatchTest extends TestCase
{
    use ContainerTestTrait;

    /**
     * @group time-sensitive
     */
    public function testClientClass()
    {
        $container = $this->buildContainer(
            [
                new MonologBundle(),
                new BankiruDoctrineApiBundle(),
            ],
            [
                'api_client' => [
                    'logger'    => ['service' => false],
                    'profiling' => true,
                ],
            ]
        );

        self::assertTrue($container->has('rpc.test_client'));
        self::assertInstanceOf(TraceableClient::class, $container->get('rpc.test_client'));

        /** @var ClientRegistryInterface $registry */
        $registry = $container->get('bankiru_api.entity_manager')->getConfiguration()->getClientRegistry();
        foreach ($registry->all() as $client) {
            self::assertInstanceOf(TraceableClient::class, $client);
        }

        $mock = $container->get('rpc.client_mock');
        $mock->push(
            $this->getSuccessResponseMock(
                (object)[
                    'id'          => 2,
                    'payload'     => 'test-payload',
                    'sub-payload' => 'sub-payload',
                ]
            ),
            function () {
                usleep(1000);

                return true;
            }
        );

        $client     = $container->get('rpc.test_client');
        $request    = new RpcRequestMock('test', []);
        $collection = $client->invoke($request);

        foreach ($collection as $response) {
            self::assertTrue($response->isSuccessful());
        }

        self::assertTrue($collection->getResponse($request)->isSuccessful());

        $stopwatch = $container->get('debug.stopwatch');
        foreach ($stopwatch->getSections() as $section) {
            foreach ($section->getEvents() as $event) {
                self::assertEquals('rpc_call', $event->getCategory());
                self::assertGreaterThan(0, $event->getMemory());
                self::assertGreaterThan(0, $event->getDuration());
                foreach ($event->getPeriods() as $period) {
                    self::assertGreaterThan(0, $period->getMemory());
                    self::assertNotNull(0, $period->getDuration());
                }
            }
        }

        $collector = $container->get('bankiru_api.profiler.collector');
        $collector->collect(new Request(), new SymfonyResponse());

        self::assertCount(1, $collector->getData());
        foreach ($collector->getData() as $profiler) {
            self::assertEquals('test_client', $profiler->getClientName());
            self::assertCount(1, $profiler->getResponses());
            self::assertCount(1, $profiler->getCalls());
        }

        self::assertEquals('api_client', $collector->getName());

    }

    /** {@inheritdoc} */
    protected function getCacheDir()
    {
        return __DIR__.'/../build/cache/';
    }

    private function getSuccessResponseMock($result)
    {
        $mock = $this->prophesize(RpcResponseInterface::class);
        $mock->isSuccessful()->willReturn(true);
        $mock->getBody()->willReturn($result);
        $mock->getError()->willReturn(null);

        return $mock->reveal();
    }
}
