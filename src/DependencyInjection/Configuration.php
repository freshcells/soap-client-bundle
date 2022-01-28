<?php

namespace Freshcells\SoapClientBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see
 * {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('freshcells_soap_client');
        if (method_exists($treeBuilder, 'getRootNode')) {
            $rootNode = $treeBuilder->getRootNode();
        } else {
            // BC layer for symfony/config < 4.2
            $rootNode = $treeBuilder->root('freshcells_soap_client');
        }

        $rootNode->children()
                    ->scalarNode('logger')->defaultFalse()->end()
                    ->arrayNode('anonymize_logs')
                    ->children()
                            ->arrayNode('elements')
                                ->scalarPrototype()->end()
                            ->end()
                            ->arrayNode('attributes')
                                ->scalarPrototype()->end()
                            ->end()
                            ->arrayNode('namespaces')
                                ->scalarPrototype()->end()
                            ->end()
                    ->end()
                    ->end()
                    ->arrayNode('truncate_element_logs')
                        ->children()
                            ->scalarNode('max_length')->defaultValue(100)->end()
                            ->arrayNode('elements')
                                ->scalarPrototype()->end()
                            ->end()
                            ->arrayNode('namespaces')
                                ->scalarPrototype()->end()
                            ->end()
                        ->end()
                    ->end()
                    ->arrayNode('truncate_logs')
                        ->children()
                            ->scalarNode('max_length')->defaultValue(100)->end()
                        ->end()
                    ->end()
                    ->scalarNode('enable_profiler')->defaultTrue()->end();

        return $treeBuilder;
    }
}
