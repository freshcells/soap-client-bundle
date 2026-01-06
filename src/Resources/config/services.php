<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();
    $parameters->set('freshcells_soap_client.soap_options', []);
    $parameters->set('freshcells_soap_client.event_dispatcher.service', 'event_dispatcher');

    $services->set(\Freshcells\SoapClientBundle\Plugin\LogPlugin::class)
        ->private()
        ->args([
            service('logger'),
            '$middleware' => [],
        ]);

    $services->set(\Freshcells\SoapClientBundle\Plugin\AnonymizerLogMiddleware::class)
        ->private()
        ->args([
            '$elements' => [],
            '$attributes' => [],
            '$substitute' => '*****',
            '$namespaces' => [],
        ]);

    $services->set(\Freshcells\SoapClientBundle\Plugin\TruncateElementLogMiddleware::class)
        ->private()
        ->args([
            '$elements' => [],
            '$namespaces' => [],
            '$maxLength' => '10',
        ]);

    $services->set(\Freshcells\SoapClientBundle\Plugin\TruncateLogMiddleware::class)
        ->private()
        ->args(['$maxLength' => '500']);
};
