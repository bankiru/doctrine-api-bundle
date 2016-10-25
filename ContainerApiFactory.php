<?php

namespace Bankiru\Api;

use Bankiru\Api\Doctrine\ApiFactoryInterface;
use Bankiru\Api\Doctrine\ApiFactoryRegistryInterface;
use Bankiru\Api\Doctrine\Mapping\ApiMetadata;
use ScayTrase\Api\Rpc\RpcClientInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

final class ContainerApiFactory implements ApiFactoryRegistryInterface
{
    /** @var  ContainerInterface */
    private $container;

    /**
     * ContainerApiFactory constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /** {@inheritdoc} */
    public function create($alias, RpcClientInterface $client, ApiMetadata $metadata)
    {
        /** @var ApiFactoryInterface $factory */
        $factory = $this->container->get($alias);

        return $factory->createApi($client, $metadata);
    }

    /** {@inheritdoc} */
    public function has($alias)
    {
        return $this->container->has($alias) && $this->container->get($alias) instanceof ApiFactoryInterface;
    }
}
