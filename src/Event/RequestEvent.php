<?php

namespace Freshcells\SoapClientBundle\Event;

use Symfony\Component\EventDispatcher\Event;

class RequestEvent extends Event
{
    /**
     * @var string
     */
    private $id;
    /**
     * @var string
     */
    private $resource;
    /**
     * @var string
     */
    private $request;

    /**
     * RequestEvent constructor.
     * @param string $id
     * @param string $resource
     * @param $request
     */
    public function __construct(string $id, string $resource, $request)
    {
        $this->id       = $id;
        $this->resource = $resource;
        $this->request  = $request;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getResource(): string
    {
        return $this->resource;
    }

    /**
     * @return mixed
     */
    public function getRequest()
    {
        return $this->request;
    }
}
