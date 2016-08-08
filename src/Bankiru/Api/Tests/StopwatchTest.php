<?php

namespace Bankiru\Api\Tests;

use Bankiru\Api\ApiBundle;
use Bankiru\Api\Client\Profiling\TraceableClient;
use Bankiru\Api\ClientRegistryInterface;
use GuzzleHttp\Psr7\Response;
use ScayTrase\Api\JsonRpc\JsonRpcRequest;
use Symfony\Bundle\MonologBundle\MonologBundle;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class StopwatchTest extends ContainerTest
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
                    'logger'    => ['id' => false],
                    'profiling' => true,
                ],
            ]
        );

        self::assertTrue($container->has('test_rpc_client'));
        self::assertInstanceOf(TraceableClient::class, $container->get('test_rpc_client'));

        /** @var ClientRegistryInterface $registry */
        $registry = $container->get('bankiru_api.entity_manager')->getConfiguration()->getRegistry();
        foreach ($registry->all() as $client) {
            self::assertInstanceOf(TraceableClient::class, $client);
        }

        $mock = $container->get('bankiru_api.test.test_guzzle_mock');
        $mock->append(
            new Response(
                200,
                [],
                json_encode(
                    [
                        'jsonrpc' => '2.0',
                        'id'      => 'test',
                        'result'  => [
                            'id'          => 2,
                            'payload'     => 'test-payload',
                            'sub-payload' => 'sub-payload',
                        ],
                    ]
                )
            )
        );

        $client     = $container->get('test_rpc_client');
        $request    = new JsonRpcRequest('test', [], 'test');
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
        self::assertCount(1, $collector->getData());
        foreach ($collector->getData() as $profiler) {
            self::assertEquals('test_client', $profiler->getClientName());
            self::assertCount(1, $profiler->getResponses());
            self::assertCount(1, $profiler->getCalls());
        }

        self::assertEquals('api_client', $collector->getName());
        $collector->collect(new Request(), new SymfonyResponse());
    }
}
