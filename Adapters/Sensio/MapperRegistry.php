<?php

namespace Bankiru\Api\Adapters\Sensio;

use Bankiru\Api\Doctrine\ApiEntityManager;
use Doctrine\Common\Persistence\ManagerRegistry;

final class MapperRegistry implements ManagerRegistry
{
    /** @var ApiEntityManager */
    private $manager;

    /**
     * MapperRegistry constructor.
     *
     * @param ApiEntityManager $manager
     */
    public function __construct(ApiEntityManager $manager)
    {
        $this->manager = $manager;
    }

    /** {@inheritdoc} */
    public function getDefaultConnectionName()
    {
        return null;
    }

    /** {@inheritdoc} */
    public function getConnection($name = null)
    {
        return null;
    }

    /**
     * Gets an array of all registered connections.
     *
     * @return array An array of Connection instances.
     */
    public function getConnections()
    {
        return [];
    }

    /** {@inheritdoc} */
    public function getConnectionNames()
    {
        return [];
    }

    /** {@inheritdoc} */
    public function getDefaultManagerName()
    {
        return 'default';
    }

    /** {@inheritdoc} */
    public function getManager($name = null)
    {
        if (null === $name || $this->getDefaultManagerName() === $name) {
            return $this->manager;
        }

        throw new \OutOfBoundsException('Invalid API entity manager: ' . $name);
    }

    /** {@inheritdoc} */
    public function getManagers()
    {
        return [$this->getDefaultManagerName() => $this->manager];
    }

    /** {@inheritdoc} */
    public function resetManager($name = null)
    {
        return $this->getManager($name);
    }

    /** {@inheritdoc} */
    public function getAliasNamespace($alias)
    {
        throw new \OutOfBoundsException('Unable to resolve alias ' . $alias);
    }

    /** {@inheritdoc} */
    public function getManagerNames()
    {
        return [$this->getDefaultManagerName()];
    }

    /** {@inheritdoc} */
    public function getRepository($persistentObject, $persistentManagerName = null)
    {
        return $this->getManager($persistentManagerName)->getRepository($persistentObject);
    }

    /** {@inheritdoc} */
    public function getManagerForClass($class)
    {
        if (!$this->manager->getMetadataFactory()->isTransient($class)) {
            return $this->manager;
        }

        return null;
    }
}
