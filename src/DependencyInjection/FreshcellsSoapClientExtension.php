<?php

namespace Freshcells\SoapClientBundle\DependencyInjection;

use Freshcells\SoapClientBundle\Plugin\AnonymizerLogMiddleware;
use Freshcells\SoapClientBundle\Plugin\LogPlugin;
use Freshcells\SoapClientBundle\Plugin\TruncateElementLogMiddleware;
use Freshcells\SoapClientBundle\Plugin\TruncateLogMiddleware;
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
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config        = $this->processConfiguration($configuration, $configs);

        foreach ($config as $key => $configVal) {
            $container->setParameter(
                'freshcells_soap_client".'.$key,
                $configVal
            );
        }

        $loader = new Loader\PhpFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.php');

        if ($config['enable_profiler']) {
            $loader->load('data_collector.php');
        }

        if ($config['logger']) {
            $subscriber  = $container->getDefinition(LogPlugin::class);
            $middlewares = [];
            if (isset($config['anonymize_logs']['elements']) || isset($config['anonymize_logs']['attributes'])) {
                $anonymizerMiddleware = $container->getDefinition(AnonymizerLogMiddleware::class);
                if (isset($config['anonymize_logs']['elements'])) {
                    $anonymizerMiddleware->replaceArgument('$elements', $config['anonymize_logs']['elements']);
                }
                if (isset($config['anonymize_logs']['attributes'])) {
                    $anonymizerMiddleware->replaceArgument('$attributes', $config['anonymize_logs']['attributes']);
                }
                if (isset($config['anonymize_logs']['namespaces'])) {
                    $anonymizerMiddleware->replaceArgument('$namespaces', $config['anonymize_logs']['namespaces']);
                }
                $middlewares[] = $anonymizerMiddleware;
            }
            if (isset($config['truncate_element_logs'])) {
                $truncateElementMiddleware = $container->getDefinition(TruncateElementLogMiddleware::class);
                if (isset($config['truncate_element_logs']['elements'])) {
                    $truncateElementMiddleware->replaceArgument(
                        '$elements',
                        $config['truncate_element_logs']['elements']
                    );
                }
                if (isset($config['anonymize_logs']['namespaces'])) {
                    $truncateElementMiddleware->replaceArgument(
                        '$namespaces',
                        $config['anonymize_logs']['namespaces']
                    );
                }
                $truncateElementMiddleware->replaceArgument(
                    '$maxLength',
                    $config['truncate_element_logs']['max_length']
                );
                $middlewares[] = $truncateElementMiddleware;
            }
            if (isset($config['truncate_logs'])) {
                $truncateMiddleware = $container->getDefinition(TruncateLogMiddleware::class);
                $truncateMiddleware->replaceArgument(
                    '$maxLength',
                    $config['truncate_logs']['max_length']
                );
                $middlewares[] = $truncateMiddleware;
            }
            $subscriber->replaceArgument(0, new Reference($config['logger']));
            if ($middlewares) {
                $subscriber->replaceArgument('$middleware', $middlewares);
            }
            $subscriber->addTag('kernel.event_subscriber');
        }
    }
}
