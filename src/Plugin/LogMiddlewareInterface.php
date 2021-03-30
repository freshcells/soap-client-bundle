<?php

namespace Freshcells\SoapClientBundle\Plugin;

interface LogMiddlewareInterface
{
    public function apply($content): string;
}
