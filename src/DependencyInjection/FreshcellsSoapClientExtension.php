<?php

namespace Freshcells\SoapClientBundle\DependencyInjection;

use Freshcells\SoapClientBundle\Plugin\LogPlugin;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class FreshcellsSoapClientExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config        = $this->processConfiguration($configuration, $configs);

        foreach ($config as $key => $configVal) {
            $container->setParameter(
                'freshcells_soap_client".'.$key,
                $configVal
            );
        }

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        if ($config['logger']) {
            $subscriber = $container->getDefinition(LogPlugin::class);
            $subscriber->replaceArgument(0, new Reference($config['logger']));
            $subscriber->addTag('kernel.event_subscriber');
        }
    }
}
