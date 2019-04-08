<?php

namespace Freshcells\SoapClientBundle\SoapClient;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

interface SoapClientInterface
{

    public function __doRequest($request, $location, $action, $version, $one_way = null);

    public function setMockRequests(array $mockRequests);

    public function setMockResponses(array $mockResponses);

    public function setDispatcher(EventDispatcherInterface $dispatcher);

}
