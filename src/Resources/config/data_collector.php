<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();

    $services->set(\Freshcells\SoapClientBundle\DataCollector\SoapCallRegistry::class)
        ->private();

    $services->set(\Freshcells\SoapClientBundle\DataCollector\SoapDataCollector::class)
        ->private()
        ->args([service(\Freshcells\SoapClientBundle\DataCollector\SoapCallRegistry::class)])
        ->tag('data_collector', ['template' => '@FreshcellsSoapClient/Collector/soap.html.twig', 'id' => 'freshcells_soap_client']);

    $services->set(\Freshcells\SoapClientBundle\Plugin\DataCollectorPlugin::class)
        ->private()
        ->args([service(\Freshcells\SoapClientBundle\DataCollector\SoapCallRegistry::class)])
        ->tag('kernel.event_subscriber');
};
