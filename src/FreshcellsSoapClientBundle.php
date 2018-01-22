<?php

namespace Freshcells\SoapClientBundle;

use Freshcells\SoapClientBundle\DependencyInjection\CompilerPass\ClientCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class FreshcellsSoapClientBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new ClientCompilerPass());
    }
}
