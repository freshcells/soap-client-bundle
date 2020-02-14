<?php


namespace Freshcells\SoapClientBundle\Event;

use Symfony\Component\EventDispatcher\Event as ComponentEvent;
use Symfony\Contracts\EventDispatcher\Event as ContractEvent;

// Symfony 4.3 BC layer
if (class_exists(ContractEvent::class)) {
// @codingStandardsIgnoreStart
    abstract class Event extends ContractEvent
    {
// @codingStandardsIgnoreEnd
    }
} else {
// @codingStandardsIgnoreStart
    abstract class Event extends ComponentEvent
    {
// @codingStandardsIgnoreEnd
    }
}
