<?php

namespace Freshcells\SoapClientBundle\Event;

use Freshcells\SoapClientBundle\Exception\SoapException;
use Symfony\Component\EventDispatcher\Event;

class FaultEvent extends Event
{
    /**
     * @var string
     */
    protected $id;
    /**
     * @var SoapException
     */
    protected $soapException;

    /**
     * @var RequestEvent
     */
    protected $requestEvent;

    /**
     * FaultEvent constructor.
     * @param string $id
     * @param SoapException $soapException
     * @param RequestEvent $requestEvent
     */
    public function __construct(string $id, SoapException $soapException, RequestEvent $requestEvent)
    {
        $this->id            = $id;
        $this->soapException = $soapException;
        $this->requestEvent  = $requestEvent;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return SoapException
     */
    public function getSoapException(): SoapException
    {
        return $this->soapException;
    }

    /**
     * @return RequestEvent
     */
    public function getRequestEvent(): RequestEvent
    {
        return $this->requestEvent;
    }
}
