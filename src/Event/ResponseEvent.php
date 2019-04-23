<?php

namespace Freshcells\SoapClientBundle\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * Class ResponseEvent
 */
class ResponseEvent extends Event
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
     * @var
     */
    private $requestContent;
    /**
     * @var
     */
    private $requestHeaders;
    /**
     * @var
     */
    private $responseContent;
    /**
     * @var
     */
    private $responseHeaders;

    /**
     * ResponseEvent constructor.
     * @param string $id
     * @param string $resource
     * @param string|null $requestContent
     * @param string|null $requestHeaders
     * @param string|null $responseContent
     * @param string|null $responseHeaders
     */
    public function __construct(
        string $id,
        string $resource,
        ?string $requestContent = null,
        ?string $requestHeaders = null,
        ?string $responseContent = null,
        ?string $responseHeaders = null
    ) {
        $this->id              = $id;
        $this->resource        = $resource;
        $this->requestContent  = $requestContent;
        $this->requestHeaders  = $requestHeaders;
        $this->responseContent = $responseContent;
        $this->responseHeaders = $responseHeaders;
    }

    /**
     * @return string
     */
    public function getId(): string
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
     * @return string
     */
    public function getRequestContent(): ?string
    {
        return $this->requestContent;
    }

    /**
     * @return string
     */
    public function getResponseContent(): ?string
    {
        return $this->responseContent;
    }

    /**
     * @return string
     */
    public function getRequestHeaders(): ?string
    {
        return $this->requestHeaders;
    }

    /**
     * @return string
     */
    public function getResponseHeaders(): ?string
    {
        return $this->responseHeaders;
    }
}
