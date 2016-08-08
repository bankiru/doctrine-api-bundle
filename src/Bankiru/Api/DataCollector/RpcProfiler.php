<?php

namespace Bankiru\Api\DataCollector;

use ScayTrase\Api\Rpc\RpcRequestInterface;
use ScayTrase\Api\Rpc\RpcResponseInterface;

class RpcProfiler
{
    /** @var RpcRequestInterface[][] */
    private $calls = [];
    /** @var RpcResponseInterface[][] */
    private $responses = [];
    /** @var  string */
    private $clientName;

    /**
     * RpcProfiler constructor.
     *
     * @param string $clientName
     */
    public function __construct($clientName) { $this->clientName = (string)$clientName; }


    /**
     * @param RpcRequestInterface|RpcRequestInterface[] $calls
     */
    public function registerCalls($calls)
    {
        if (!is_array($calls)) {
            $calls = [$calls];
        }

        $time = microtime(true);
        foreach ($calls as $call) {
            $this->calls[spl_object_hash($call)]['start']   = $time;
            $this->calls[spl_object_hash($call)]['request'] = $call;
        }
    }

    /**
     * @param RpcResponseInterface $response
     * @param RpcRequestInterface  $request
     */
    public function registerResponse(RpcResponseInterface $response, RpcRequestInterface $request = null)
    {
        $time = microtime(true);

        if (null === $request) {
            $this->responses[]['response'] = $response;

            return;
        }

        $this->calls[spl_object_hash($request)]['stop']     = $time;
        $this->calls[spl_object_hash($request)]['response'] = $response;
    }

    /**
     * @return RpcRequestInterface[][]
     */
    public function getCalls()
    {
        return $this->calls;
    }

    /**
     * @return RpcResponseInterface[][]
     */
    public function getResponses()
    {
        return $this->responses;
    }

    /**
     * @return string
     */
    public function getClientName()
    {
        return $this->clientName;
    }
}
