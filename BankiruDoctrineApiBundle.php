<?php

namespace Bankiru\Api;

use Bankiru\Api\DependencyInjection\BankiruDoctrineApiExtension;
use Bankiru\Api\DependencyInjection\Compiler\ApiFactoryPass;
use Bankiru\Api\DependencyInjection\Compiler\ClientCollectorPass;
use Bankiru\Api\DependencyInjection\Compiler\LoggerDecoratorPass;
use Bankiru\Api\DependencyInjection\Compiler\MappingCollectorPass;
use Bankiru\Api\DependencyInjection\Compiler\ProfilerDecoratorPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class BankiruDoctrineApiBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new ClientCollectorPass());
        $container->addCompilerPass(new MappingCollectorPass());
        $container->addCompilerPass(new LoggerDecoratorPass());
        $container->addCompilerPass(new ProfilerDecoratorPass());
        $container->addCompilerPass(new ApiFactoryPass());
    }

    public function getContainerExtension()
    {
        return new BankiruDoctrineApiExtension();
    }
}
