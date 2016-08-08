<?php

namespace Bankiru\Api\Tests;

use Bankiru\Api\DependencyInjection\ApiExtension;
use Bankiru\Api\DependencyInjection\Configuration;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    public function testConfigParsing()
    {
        $configuration = new Configuration();
        $processor     = new Processor();
        $rawConfigs    = [
            [
                'cache' => null,
            ],
            [
                'cache' => 'test_cache',
            ],
            [
                'cache' => [
                    'logger' => 'logger',
                ],
            ],
            [
                'cache' =>
                    [
                        'configuration' => [
                            'TestEntity' => [
                                'ttl' => 10,
                            ],
                        ],
                    ],
            ],
            [
                'cache' =>
                    [
                        'configuration' => [
                            'TestEntity' => 900,
                        ],
                    ],
            ],
            [
                'cache' =>
                    [
                        'configuration' => [
                            'TestEntity' => [
                                'enabled' => false,
                            ],
                        ],
                    ],
            ],
        ];
        $configs       = $processor->processConfiguration(
            $configuration,
            $rawConfigs
        );

        self::assertTrue($configs['cache']['enabled']);
        self::assertEquals('test_cache', $configs['cache']['service']);
        self::assertFalse($configs['cache']['configuration']['TestEntity']['enabled']);
        self::assertEquals(900, $configs['cache']['configuration']['TestEntity']['ttl']);
        self::assertEquals('logger', $configs['cache']['logger']);

        $builder = new ContainerBuilder();
        $extension = new ApiExtension();
        $extension->load($rawConfigs, $builder);
    }
}
