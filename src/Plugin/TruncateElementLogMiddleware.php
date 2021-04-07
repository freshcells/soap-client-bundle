<?php

namespace Freshcells\SoapClientBundle\Plugin;

class TruncateElementLogMiddleware implements LogMiddlewareInterface
{
    private array $elements = [];
    private array $namespaces = [];
    private int $maxLength;

    public function __construct(array $elements, array $namespaces, int $maxLength)
    {
        $this->elements   = $elements;
        $this->namespaces = $namespaces;
        $this->maxLength  = $maxLength;
    }

    public function apply($content): string
    {
        $doc = new \DOMDocument();
        $doc->loadXML($content);
        $xpath = new \DOMXPath($doc);
        foreach ($this->namespaces as $namespace => $uri) {
            $xpath->registerNamespace($namespace, $uri);
        }

        foreach ($this->elements as $field) {
            $query   = '//'.$field.'/text()';
            $entries = $xpath->query($query);
            foreach ($entries as $entry) {
                if (strlen($entry->data) > $this->maxLength) {
                    $entry->data = trim(substr($entry->data, 0, $this->maxLength)).'...';
                }
            }
        }

        return $doc->saveXml();
    }
}
