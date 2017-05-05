<?php

namespace Bankiru\Api\Tests;

use Bankiru\Api\DependencyInjection\BankiruDoctrineApiExtension;
use Bankiru\Api\DependencyInjection\Configuration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class ConfigurationTest extends TestCase
{
    public function testEntityCacheConfigParsing()
    {
        $configuration = new Configuration();
        $processor     = new Processor();
        $rawConfigs    = [
            [
                'entity_cache'   => null,
            ],
            [
                'entity_cache'   => 'test_cache',

            ],
            [
                'entity_cache'   => [
                    'logger' => 'logger',
                ],
            ],
            [
                'entity_cache'   =>
                    [
                        'configuration' => [
                            'TestEntity' => [
                                'ttl'   => 10,
                                'extra' => [
                                    'quick_search' => false,
                                ],
                            ],
                        ],
                    ],
            ],
            [
                'entity_cache'   =>
                    [
                        'configuration' => [
                            'TestEntity' => 900,
                        ],
                    ],
            ],
            [
                'entity_cache'   =>
                    [
                        'configuration' => [
                            'TestEntity' => [
                                'enabled' => false,
                                'extra'   => [
                                    'quick_search' => true,
                                ],
                            ],
                        ],
                    ],
            ],
        ];
        $configs       = $processor->processConfiguration(
            $configuration,
            $rawConfigs
        );

        self::assertTrue($configs['entity_cache']['enabled']);
        self::assertEquals('test_cache', $configs['entity_cache']['service']);
        self::assertFalse($configs['entity_cache']['configuration']['TestEntity']['enabled']);
        self::assertEquals(900, $configs['entity_cache']['configuration']['TestEntity']['ttl']);
        self::assertTrue($configs['entity_cache']['configuration']['TestEntity']['extra']['quick_search']);
        self::assertEquals('logger', $configs['entity_cache']['logger']);

        $builder   = new ContainerBuilder();
        $extension = new BankiruDoctrineApiExtension();
        $extension->load($rawConfigs, $builder);
    }
}
