<?php
/**
 * Created by PhpStorm.
 * User: batanov.pavel
 * Date: 09.02.2016
 * Time: 16:43
 */

namespace Bankiru\Api\Tests;

use Bankiru\Api\ApiBundle;
use Bankiru\Api\Doctrine\EntityMetadataFactory;
use Doctrine\Common\Persistence\ObjectManager;

class BundleTest extends ContainerTest
{
    public function testExtensionLoading()
    {
        $container = $this->buildContainer(
            [
                new ApiBundle(),
            ],
            []
        );

        self::assertTrue($container->has('bankiru_api.entity_manager'));
        self::assertInstanceOf(ObjectManager::class, $container->get('bankiru_api.entity_manager'));
        self::assertInstanceOf(EntityMetadataFactory::class, $container->get('bankiru_api.entity_manager')->getMetadataFactory());
    }
}
