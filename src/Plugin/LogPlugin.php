<?php

namespace Freshcells\SoapClientBundle\Plugin;

use Freshcells\SoapClientBundle\Event\Events;
use Freshcells\SoapClientBundle\Event\FaultEvent;
use Freshcells\SoapClientBundle\Event\RequestEvent;
use Freshcells\SoapClientBundle\Event\ResponseEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class LogPlugin
 * @package Freshcells\SoapClientBundle\Plugin
 */
class LogPlugin implements EventSubscriberInterface
{
    private LoggerInterface $logger;
    /**
     * @var LogMiddlewareInterface[]
     */
    private array $middleware;

    /**
     * LogPlugin constructor.
     * @param LoggerInterface $logger
     * @param LogMiddlewareInterface[] $middleware
     */
    public function __construct(LoggerInterface $logger, array $middleware = [])
    {
        $this->logger     = $logger;
        $this->middleware = $middleware;
    }

    /**
     * @param RequestEvent $event
     */
    public function onClientRequest(RequestEvent $event)
    {
        $this->logger->info(sprintf(
            '[freshcells/soap-client-bundle] pre request: about to call "%s" with params %s',
            $event->getResource(),
            $this->applyMiddleware(print_r($event->getRequest(), true))
        ));
    }

    /**
     * @param ResponseEvent $event
     */
    public function onClientResponse(ResponseEvent $event)
    {
        $this->logger->info(sprintf(
            '[freshcells/soap-client-bundle] request: %s %s',
            print_r($event->getRequestHeaders(), true),
            $this->applyMiddleware(print_r($event->getRequestContent(), true))
        ));
        $this->logger->info(sprintf(
            '[freshcells/soap-client-bundle] response: %s %s',
            print_r($event->getResponseHeaders(), true),
            $this->applyMiddleware(print_r($event->getResponseContent(), true))
        ));
    }

    /**
     * @param FaultEvent $event
     */
    public function onClientFault(FaultEvent $event)
    {
        $this->logger->error(sprintf(
            '[freshcells/soap-client-bundle] fault "%s" for request "%s" with params %s',
            $event->getException()->getMessage(),
            $event->getRequestEvent()->getResource(),
            $this->applyMiddleware(print_r($event->getRequestEvent()->getRequest(), true))
        ));
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

    /**
     * @param string $content
     * @return string
     */
    protected function applyMiddleware(string $content): string
    {
        foreach ($this->middleware as $middleware) {
            $content = $middleware->apply($content);
        }

        return $content;
    }
}
