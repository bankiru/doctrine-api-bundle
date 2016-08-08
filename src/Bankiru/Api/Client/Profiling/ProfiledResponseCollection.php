<?php

namespace Bankiru\Api\Client\Profiling;

use Bankiru\Api\DataCollector\RpcProfiler;
use ScayTrase\Api\Rpc\ResponseCollectionInterface;
use ScayTrase\Api\Rpc\RpcRequestInterface;

class ProfiledResponseCollection implements \IteratorAggregate, ResponseCollectionInterface
{
    /** @var  ResponseCollectionInterface */
    private $collection;
    /** @var  RpcProfiler */
    private $profiler;

    /**
     * ProfiledResponseCollection constructor.
     *
     * @param ResponseCollectionInterface $collection
     * @param RpcProfiler                 $profiler
     */
    public function __construct(ResponseCollectionInterface $collection, RpcProfiler $profiler)
    {
        $this->collection = $collection;
        $this->profiler   = $profiler;
    }


    /** {@inheritdoc} */
    public function getResponse(RpcRequestInterface $request)
    {
        $response = $this->collection->getResponse($request);
        $this->profiler->registerResponse($response, $request);

        return $response;
    }

    /** {@inheritdoc} */
    public function getIterator()
    {
        foreach ($this->collection as $response) {
            $this->profiler->registerResponse($response);
            yield $response;
        }
    }
}
