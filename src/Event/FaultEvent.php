<?php

namespace Freshcells\SoapClientBundle\Event;

class FaultEvent extends Event
{
    /**
     * @var string
     */
    protected $id;
    /**
     * @var \Exception
     */
    protected $exception;

    /**
     * @var RequestEvent
     */
    protected $requestEvent;

    /**
     * FaultEvent constructor.
     * @param string $id
     * @param \Exception $exception
     * @param RequestEvent $requestEvent
     */
    public function __construct(string $id, \Exception $exception, RequestEvent $requestEvent)
    {
        $this->id           = $id;
        $this->exception    = $exception;
        $this->requestEvent = $requestEvent;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return \Exception
     */
    public function getException(): \Exception
    {
        return $this->exception;
    }

    /**
     * @return RequestEvent
     */
    public function getRequestEvent(): RequestEvent
    {
        return $this->requestEvent;
    }
}
