<?php

namespace Freshcells\SoapClientBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ClientCompilerPass implements CompilerPassInterface
{
    const CLIENT_TAG = 'freshcells_soap_client.client';

    public function process(ContainerBuilder $container): void
    {
        $clients = $container->findTaggedServiceIds(self::CLIENT_TAG);

        foreach ($clients as $clientId => $tags) {
            if (count($tags) > 1) {
                throw new \LogicException(sprintf('Clients should use a single \'%s\' tag', self::CLIENT_TAG));
            }

            $clientDefinition = $container->findDefinition($clientId);
            if (isset($tags[0]['no_dispatcher']) === false) {
                $clientDefinition->addMethodCall(
                    'setDispatcher',
                    [
                        new Reference($container->getParameter('freshcells_soap_client.event_dispatcher.service'))
                    ]
                );
            }
        }
    }
}
