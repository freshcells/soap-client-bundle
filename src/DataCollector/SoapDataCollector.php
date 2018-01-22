<?php

namespace Freshcells\SoapClientBundle\DataCollector;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

class SoapDataCollector extends DataCollector
{
    private $callRegistry;

    /**
     * SoapDataCollector constructor.
     * @param SoapCallRegistry $callRegistry
     */
    public function __construct(SoapCallRegistry $callRegistry)
    {
        $this->callRegistry = $callRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        $time = 0;
        foreach ($this->callRegistry->getCalls() as $call) {
            $time += (isset($call['duration']) ? $call['duration'] : 0);
        }

        $this->data = [
            'total'    => count($this->callRegistry->getCalls()),
            'time'     => $time,
            'requests' => $this->callRegistry->getCalls(),
        ];
    }

    public function reset()
    {
        $this->data = [
            'total'    => 0,
            'time'     => 0,
            'requests' => [],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'freshcells_soap_client';
    }

    /**
     * @return int
     */
    public function getTotal()
    {
        return $this->data['total'];
    }

    /**
     * @return int Time in milliseconds.
     */
    public function getTime()
    {
        return $this->data['time'];
    }

    /**
     * @return array
     */
    public function getRequests()
    {
        return $this->data['requests'];
    }
}
