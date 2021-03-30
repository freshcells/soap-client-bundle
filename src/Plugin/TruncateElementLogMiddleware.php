<?php

namespace Freshcells\SoapClientBundle\Plugin;

class TruncateElementLogMiddleware implements LogMiddlewareInterface
{
    private array $elements = [];
    private int $maxLength;

    /**
     * TruncateElementLogMiddleware constructor.
     * @param array $elements
     * @param int $maxLength
     */
    public function __construct(array $elements, int $maxLength)
    {
        $this->elements  = $elements;
        $this->maxLength = $maxLength;
    }

    public function apply($content): string
    {
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
