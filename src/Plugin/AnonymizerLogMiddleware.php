<?php

namespace Freshcells\SoapClientBundle\Plugin;

class AnonymizerLogMiddleware implements LogMiddlewareInterface
{
    private array $elements = [];
    private array $attributes = [];
    private string $substitute;
    private array $headersToSubstitute = [];

    public function __construct(
        array $elements,
        array $attributes,
        $substitute = '*****',
        array $headersToSubstitute = []
    ) {
        $this->elements            = $elements;
        $this->attributes          = $attributes;
        $this->substitute          = $substitute;
        $this->headersToSubstitute = $headersToSubstitute;
    }

    public function apply($content): string
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
}
