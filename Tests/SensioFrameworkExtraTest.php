<?php

namespace Bankiru\Api\Tests;

use Bankiru\Api\BankiruDoctrineApiBundle;
use Bankiru\Api\Doctrine\Test\Entity\TestEntity;
use Bankiru\Api\Doctrine\Test\TestBundle;
use PHPUnit\Framework\TestCase;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle;
use Symfony\Component\HttpKernel\Tests\Fixtures\KernelForTest;

final class SensioFrameworkExtraTest extends TestCase
{
    use ContainerTestTrait;

    public function testParamConverterExists()
    {
        $container = $this->buildContainer(
            [
                new BankiruDoctrineApiBundle(),
                new SensioFrameworkExtraBundle(),
                new TestBundle(),
            ],
            [],
            false
        );
        $container->set('kernel', new KernelForTest('test', true));
        $container = $this->compile($container);

        self::assertTrue($container->has('bankiru_api.sensio_bridge.doctrine_param_converter'));

        $configuration = new ParamConverter([]);
        $configuration->setClass(TestEntity::class);

        $container->get('bankiru_api.sensio_bridge.doctrine_param_converter')->supports($configuration);
    }

    public function testParamConverterExistsDoesNotExistWithoutBundle()
    {
        $container = $this->buildContainer(
            [
                new BankiruDoctrineApiBundle(),
                new TestBundle(),
            ],
            [],
            false
        );

        $container = $this->compile($container);

        self::assertFalse($container->has('bankiru_api.sensio_bridge.doctrine_param_converter'));
    }

    /** {@inheritdoc} */
    protected function getCacheDir()
    {
        return __DIR__.'/../build/cache/';
    }
}
