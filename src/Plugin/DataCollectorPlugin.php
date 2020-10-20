<?php

namespace Freshcells\SoapClientBundle\Plugin;

use Freshcells\SoapClientBundle\DataCollector\SoapCallRegistry;
use Freshcells\SoapClientBundle\Event\Events;
use Freshcells\SoapClientBundle\Event\FaultEvent;
use Freshcells\SoapClientBundle\Event\RequestEvent;
use Freshcells\SoapClientBundle\Event\ResponseEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class DataCollectorPlugin implements EventSubscriberInterface
{
    /**
     * @var SoapCallRegistry
     */
    private $registry;

    /**
     * @param SoapCallRegistry $registry
     */
    public function __construct(SoapCallRegistry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * @param RequestEvent $event
     */
    public function onClientRequest(RequestEvent $event)
    {
        $this->registry->addRequest($event);
    }

    /**
     * @param ResponseEvent $event
     */
    public function onClientResponse(ResponseEvent $event)
    {
        $this->registry->addResponse($event);
    }

    /**
     * @param FaultEvent $event
     */
    public function onClientFault(FaultEvent $event)
    {
        $this->registry->addRequest($event->getRequestEvent());
        //todo use a dedicated fault rendering in profiler
        $this->registry->addResponse(
            new ResponseEvent(
                $event->getId(),
                $event->getRequestEvent()->getResource(),
                $event->getRequestEvent()->getRequest(),
                null,
                $event->getException()->getMessage(),
                null
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            Events::REQUEST  => 'onClientRequest',
            Events::RESPONSE => 'onClientResponse',
            Events::FAULT    => 'onClientFault'
        ];
    }
}
