<?php
/**
 * Created by PhpStorm.
 * User: batanov.pavel
 * Date: 21.03.2016
 * Time: 13:33
 */

namespace Bankiru\Api\DataCollector;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollectorInterface;

class ClientProfilingCollector implements DataCollectorInterface
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
        //collects data with profilers
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

    /**  */
    public function getData()
    {
        return $this->profilers;
    }

    public function addProfiler(RpcProfiler $profiler)
    {
        $this->profilers[] = $profiler;
    }
}
