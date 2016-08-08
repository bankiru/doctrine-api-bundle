<?php

namespace Bankiru\Api\Client\Profiling;

use Bankiru\Api\DataCollector\RpcProfiler;
use ScayTrase\Api\Rpc\RpcClientInterface;

class ProfiledClient implements RpcClientInterface
{
    /** @var  RpcClientInterface */
    private $client;
    /** @var  RpcProfiler */
    private $profiler;

    /**
     * ProfiledClient constructor.
     *
     * @param RpcClientInterface $client
     * @param RpcProfiler        $profiler
     */
    public function __construct(RpcClientInterface $client, RpcProfiler $profiler)
    {
        $this->client   = $client;
        $this->profiler = $profiler;
    }


    /** {@inheritdoc} */
    public function invoke($calls)
    {
        $this->profiler->registerCalls($calls);

        return new ProfiledResponseCollection($this->client->invoke($calls), $this->profiler);
    }
}
