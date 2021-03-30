<?php

namespace Freshcells\SoapClientBundle\Tests;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class MiddlewareAppKernel extends AppKernel
{
    protected function configureContainer(ContainerBuilder $containerBuilder, LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config_middleware.yml');
    }
}
