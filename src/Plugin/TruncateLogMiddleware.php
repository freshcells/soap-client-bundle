<?php

namespace Freshcells\SoapClientBundle\Plugin;

class TruncateLogMiddleware implements LogMiddlewareInterface
{
    private int $maxLength;

    public function __construct(int $maxLength)
    {
        $this->maxLength = $maxLength;
    }

    public function apply($content): string
    {
        return substr($content, 0, $this->maxLength);
    }
}
