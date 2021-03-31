<?php

namespace Freshcells\SoapClientBundle\Plugin;

class TruncateElementLogMiddleware implements LogMiddlewareInterface
{
    private array $elements = [];
    private int $maxLength;

    public function __construct(array $elements, int $maxLength)
    {
        $this->elements  = $elements;
        $this->maxLength = $maxLength;
    }

    public function apply($content): string
    {
        // todo with regex its not relyibly working, probably should use xpath
        foreach ($this->elements as $field) {
            $content = preg_replace_callback(
                '/<('.$field.'[^>]*)>(.{'.$this->maxLength.',}?)<\/'.$field.'>/msi',
                function ($hit) use ($field) {
                    $replaced = trim(substr($hit[2], 0, $this->maxLength)).'...';

                    return sprintf(
                        '<%s>%s</%s>',
                        $hit[1],
                        $replaced,
                        $field
                    );
                },
                $content
            );
        }

        return $content;
    }
}
