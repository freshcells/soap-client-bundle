<?php

namespace Freshcells\SoapClientBundle\Plugin;

class AnonymizerLogMiddleware implements LogMiddlewareInterface
{
    private array $elements = [];
    private array $attributes = [];
    private string $substitute;
    private array $namespaces = [];

    public function __construct(
        array $elements,
        array $attributes,
        string $substitute = '*****',
        array $namespaces = []
    ) {
        $this->elements   = $elements;
        $this->attributes = $attributes;
        $this->substitute = $substitute;
        $this->namespaces = $namespaces;
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
                $entry->data = $this->substitute;
            }
        }

        foreach ($this->attributes as $attribute) {
            $entries = $xpath->query('//'.$attribute);
            foreach ($entries as $entry) {
                foreach ($entry->attributes as $attribute) {
                    $attribute->value = $this->substitute;
                }
            }
        }

        return $doc->saveXml();
    }
}
