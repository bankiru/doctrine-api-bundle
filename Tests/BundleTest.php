<?php

namespace Bankiru\Api\Tests;

use Bankiru\Api\BankiruDoctrineApiBundle;
use Bankiru\Api\Doctrine\EntityMetadataFactory;
use Bankiru\Api\Doctrine\Test\TestApi;
use Doctrine\Common\Persistence\ObjectManager;
use PHPUnit\Framework\TestCase;

final class BundleTest extends TestCase
{
    use ContainerTestTrait;

    public function testExtensionLoading()
    {
        $container = $this->buildContainer(
            [
                new BankiruDoctrineApiBundle(),
            ],
            []
        );

        self::assertTrue($container->has('bankiru_api.entity_manager'));
        self::assertInstanceOf(ObjectManager::class, $container->get('bankiru_api.entity_manager'));
        self::assertInstanceOf(
            EntityMetadataFactory::class,
            $container->get('bankiru_api.entity_manager')->getMetadataFactory()
        );

        self::assertTrue($container->has('bankiru_api.factory_registry'));
        self::assertTrue($container->get('bankiru_api.factory_registry')->has(TestApi::class));
    }

    /** {@inheritdoc} */
    protected function getCacheDir()
    {
        return __DIR__.'/../build/cache/';
    }
}
