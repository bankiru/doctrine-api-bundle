<?php

namespace Bankiru\Api\Tests;

use ScayTrase\Api\Rpc\RpcClientInterface;

final class TestClient implements RpcClientInterface
{
    /** @var RpcClientInterface */
    private $delegate;

    /**
     * TestClient constructor.
     *
     * @param RpcClientInterface $delegate
     */
    public function __construct(RpcClientInterface $delegate)
    {
        $this->delegate = $delegate;
    }

    /** {@inheritdoc} */
    public function invoke($calls)
    {
        return $this->delegate->invoke($calls);
    }
}
