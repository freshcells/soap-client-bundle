<?php

namespace Freshcells\SoapClientBundle\Exception;

class SoapException extends \Exception
{
    /**
     * @param \Throwable $throwable
     *
     * @return SoapException
     */
    public static function fromThrowable(\Throwable $throwable)
    {
        return new self($throwable->getMessage(), $throwable->getCode(), $throwable);
    }
}
