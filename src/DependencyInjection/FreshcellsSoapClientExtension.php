<?php

namespace Freshcells\SoapClientBundle\DependencyInjection;

use Freshcells\SoapClientBundle\Plugin\AnonymizerLogPlugin;
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

        if ($config['enable_profiler']) {
            $loader->load('data_collector.xml');
        }

        if ($config['logger']) {
            $subscriber = $container->getDefinition(LogPlugin::class);
            if (isset($config['anonymize_logs']['elements']) || isset($config['anonymize_logs']['attributes'])) {
                $subscriber = $container->getDefinition(AnonymizerLogPlugin::class);
                if (isset($config['anonymize_logs']['elements'])) {
                    $subscriber->replaceArgument('$elements', $config['anonymize_logs']['elements']);
                }
                if (isset($config['anonymize_logs']['attributes'])) {
                    $subscriber->replaceArgument('$attributes', $config['anonymize_logs']['attributes']);
                }
            }
            $subscriber->replaceArgument(0, new Reference($config['logger']));
            $subscriber->addTag('kernel.event_subscriber');
        }
    }
}
