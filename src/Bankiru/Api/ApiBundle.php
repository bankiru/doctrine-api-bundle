<?php

namespace Bankiru\Api;

use Bankiru\Api\DependencyInjection\ApiExtension;
use Bankiru\Api\DependencyInjection\Compiler\ClientCollectorPass;
use Bankiru\Api\DependencyInjection\Compiler\LoggerDecoratorPass;
use Bankiru\Api\DependencyInjection\Compiler\MappingCollectorPass;
use Bankiru\Api\DependencyInjection\Compiler\ProfilerDecoratorPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class ApiBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new ClientCollectorPass());
        $container->addCompilerPass(new MappingCollectorPass());
        $container->addCompilerPass(new LoggerDecoratorPass());
        $container->addCompilerPass(new ProfilerDecoratorPass());
    }

    public function getContainerExtension()
    {
        return new ApiExtension();
    }
}
