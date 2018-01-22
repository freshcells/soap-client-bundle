<?php

namespace Freshcells\SoapClientBundle\DataCollector;

use Freshcells\SoapClientBundle\Event\RequestEvent;
use Freshcells\SoapClientBundle\Event\ResponseEvent;

class SoapCallRegistry
{
    /**
     * @var array
     */
    private $calls = [];

    /**
     * @param RequestEvent $event
     */
    public function addRequest(RequestEvent $event)
    {
        $request                      = array(
            'resource'         => $event->getResource(),
            'request_headers'  => '',
            'request_body'     => $this->prettyXML($event->getRequest()),
            'response_headers' => '',
            'response_body'    => '',
            'start'            => microtime(true),
        );
        $this->calls[$event->getId()] = $request;
    }

    /**
     * @param ResponseEvent $event
     */
    public function addResponse(ResponseEvent $event)
    {
        $id                                   = $event->getId();
        $response                             = $event->getResponseContent() ?
            $this->prettyXML($event->getResponseContent()) :
            $event->getResponseContent();
        $this->calls[$id]['end']              = microtime(true);
        $this->calls[$id]['request_headers']  = $event->getRequestHeaders();
        $this->calls[$id]['request_body']     = $this->prettyXML($event->getRequestContent());
        $this->calls[$id]['response_headers'] = $event->getResponseHeaders();
        $this->calls[$id]['response_body']    = $response;
        $this->calls[$id]['duration']         = ($this->calls[$id]['end'] - $this->calls[$id]['start']);
    }

    /**
     * @param string $xml
     * @return string
     */
    private function prettyXML(string $xml): string
    {
        try {
            //nicen
            $doc = new \DomDocument('1.0');
            $doc->loadXML($xml);
            $doc->formatOutput = true;

            return $doc->saveXML();
        } catch (\Exception $e) {
            // probably no xml, just let it pass
        }

        return $xml;
    }

    /**
     * @return array
     */
    public function getCalls(): array
    {
        return $this->calls;
    }
}
