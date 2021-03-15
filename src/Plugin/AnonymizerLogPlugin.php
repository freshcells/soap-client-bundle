<?php

namespace Freshcells\SoapClientBundle\Plugin;

use Freshcells\SoapClientBundle\Event\Events;
use Freshcells\SoapClientBundle\Event\FaultEvent;
use Freshcells\SoapClientBundle\Event\RequestEvent;
use Freshcells\SoapClientBundle\Event\ResponseEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AnonymizerLogPlugin implements EventSubscriberInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;
    private $elements = [];
    private $attributes = [];
    private $substitute;
    private $headersToSubstitute = [];

    /**
     * AnonymizerLogPlugin constructor.
     * @param LoggerInterface $logger
     * @param string[] $elements
     * @param string[] $attributes
     * @param string $substitute
     * @param string[] $headersToSubstitute
     */
    public function __construct(
        LoggerInterface $logger,
        array $elements,
        array $attributes,
        $substitute = '*****',
        array $headersToSubstitute = []
    ) {
        $this->logger              = $logger;
        $this->elements            = $elements;
        $this->attributes          = $attributes;
        $this->substitute          = $substitute;
        $this->headersToSubstitute = $headersToSubstitute;
    }

    /**
     * @param RequestEvent $event
     */
    public function onClientRequest(RequestEvent $event)
    {
        $content = $this->anonymize(
            print_r($event->getRequest(), true)
        );

        $this->logger->info(
            sprintf(
                '[freshcells/soap-client-bundle] pre request: about to call "%s" with params %s',
                $event->getResource(),
                $content,
                true
            )
        );
    }

    /**
     * @param ResponseEvent $event
     */
    public function onClientResponse(ResponseEvent $event)
    {
        $requestContent = $this->anonymize(print_r($event->getRequestContent(), true));
        $this->logger->info(sprintf(
            '[freshcells/soap-client-bundle] request: %s %s',
            print_r($event->getRequestHeaders(), true),
            $requestContent
        ));
        $responseContent = $this->anonymize(print_r($event->getResponseContent(), true));
        $this->logger->info(sprintf(
            '[freshcells/soap-client-bundle] response: %s %s',
            print_r($event->getResponseHeaders(), true),
            $responseContent
        ));
    }

    /**
     * @param FaultEvent $event
     */
    public function onClientFault(FaultEvent $event)
    {
        $requestContent = $this->anonymizeElements(print_r($event->getRequestContent(), true));
        $this->logger->error(sprintf(
            '[freshcells/soap-client-bundle] fault "%s" for request "%s" with params %s',
            $event->getException()->getMessage(),
            $event->getRequestEvent()->getResource(),
            $requestContent
        ));
    }

    protected function anonymize(string $content)
    {
        //elements
        foreach ($this->elements as $field) {
            $content = preg_replace(
                sprintf('/<(%s[^>]*)>.*?<\/(%s)>/i', $field, $field),
                sprintf('<%s>%s</%s>', '$1', $this->substitute, '$2'),
                $content
            );
        }

        //attributes
        foreach ($this->attributes as $attribute) {
            $re    = '/ '.$attribute.'="[^"]*/';
            $subst = ' '.$attribute.'="'.$this->substitute;

            $content = preg_replace($re, $subst, $content);
        }

        return $content;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            Events::REQUEST  => 'onClientRequest',
            Events::RESPONSE => 'onClientResponse',
            Events::FAULT    => 'onClientFault',
        ];
    }
}
