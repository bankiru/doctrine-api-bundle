<?php
/**
 * Created by PhpStorm.
 * User: batanov.pavel
 * Date: 21.03.2016
 * Time: 13:36
 */

namespace Bankiru\Api\Client\Profiling;

use ScayTrase\Api\Rpc\RpcClientInterface;
use Symfony\Component\Stopwatch\Stopwatch;

class TraceableClient implements RpcClientInterface
{
    /** @var  RpcClientInterface */
    private $client;
    /** @var  Stopwatch */
    private $stopwatch;
    /** @var  string */
    private $clientName;

    /**
     * TraceableClient constructor.
     *
     * @param RpcClientInterface $client
     * @param Stopwatch          $stopwatch
     * @param string             $clientName
     */
    public function __construct(RpcClientInterface $client, Stopwatch $stopwatch, $clientName = 'api_client')
    {
        $this->client     = $client;
        $this->stopwatch  = $stopwatch;
        $this->clientName = (string)$clientName;
    }

    /** {@inheritdoc} */
    public function invoke($calls)
    {
        $this->stopwatch->start($this->clientName, 'rpc_call');
        $collection = new TraceableResponseCollection(
            $this->client->invoke($calls),
            $this->stopwatch,
            $this->clientName
        );
        $this->stopwatch->stop($this->clientName);

        return $collection;
    }
}
