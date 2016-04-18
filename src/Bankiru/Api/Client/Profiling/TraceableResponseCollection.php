<?php
/**
 * Created by PhpStorm.
 * User: batanov.pavel
 * Date: 21.03.2016
 * Time: 13:36
 */

namespace Bankiru\Api\Client\Profiling;

use ScayTrase\Api\Rpc\ResponseCollectionInterface;
use ScayTrase\Api\Rpc\RpcRequestInterface;
use Symfony\Component\Stopwatch\Stopwatch;

class TraceableResponseCollection implements \IteratorAggregate, ResponseCollectionInterface
{
    /** @var  ResponseCollectionInterface */
    private $collection;
    /** @var  Stopwatch */
    private $stopwatch;
    /** @var  string */
    private $client;

    /**
     * TraceableResponseCollection constructor.
     *
     * @param ResponseCollectionInterface $collection
     * @param Stopwatch                   $stopwatch
     * @param string                      $client
     */
    public function __construct(
        ResponseCollectionInterface $collection,
        Stopwatch $stopwatch,
        $client)
    {
        $this->collection = $collection;
        $this->stopwatch  = $stopwatch;
        $this->client     = $client;
    }

    /** {@inheritdoc} */
    public function getResponse(RpcRequestInterface $request)
    {
        $this->stopwatch->start($this->client, 'rpc_response');
        $response = $this->collection->getResponse($request);
        $this->stopwatch->stop($this->client);

        return $response;
    }

    /** {@inheritdoc} */
    public function getIterator()
    {
        /** @var \Iterator $iterator */
        $iterator = null;
        do {
            $this->stopwatch->start($this->client, 'rpc_response');
            if (null === $iterator) {
                if ($this->collection instanceof \IteratorAggregate) {
                    $iterator = $this->collection->getIterator();
                } elseif ($this->collection instanceof \Iterator) {
                    $iterator = $this->collection;
                }
                $iterator->rewind();
            }
            $value = $iterator->current();
            $this->stopwatch->start($this->client, 'rpc_response');
            $iterator->next();

            yield $value;
        } while ($iterator->valid());
    }
}
