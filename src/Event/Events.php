<?php

namespace Freshcells\SoapClientBundle\Event;

class Events
{
    /**
     * Fired before a request is executed
     */
    const REQUEST = 'freshcells_soap_client.request.pre_event';

    /**
     * Fired after a request is executed
     */
    const RESPONSE = 'freshcells_soap_client.request.post_event';

    /**
     * Fired in case of fault
     */
    const FAULT = 'freshcells_soap_client.request.fault_event';
}
