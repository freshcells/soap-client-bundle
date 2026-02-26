<?php

namespace Freshcells\SoapClientBundle\SoapClient;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

interface SoapClientInterface
{
    public function __doRequest(
        string $request,
        string $location,
        string $action,
        int $version,
        bool $oneWay = false,
        ?string $uriParserClass = null
    ): ?string;

    public function setMockRequests(array $mockRequests);

    public function setMockResponses(array $mockResponses);

    public function setDispatcher(EventDispatcherInterface $dispatcher);

    public function getOptions(): array;
}
