<?php

namespace Bankiru\Api\DataCollector;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

class ClientProfilingCollector extends DataCollector
{
    /** @var RpcProfiler[] */
    private $profilers = [];

    /**
     * Collects data for the given Request and Response.
     *
     * @param Request    $request   A Request instance
     * @param Response   $response  A Response instance
     * @param \Exception $exception An Exception instance
     */
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        $this->data      = $this->profilers;
        $this->profilers = [];
    }

    /**
     * Returns the name of the collector.
     *
     * @return string The collector name
     */
    public function getName()
    {
        return 'api_client';
    }

    public function getData()
    {
        return $this->data;
    }

    public function addProfiler(RpcProfiler $profiler)
    {
        $this->profilers[$profiler->getName()] = $profiler;
    }
}
