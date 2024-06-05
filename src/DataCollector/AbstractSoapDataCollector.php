<?php

namespace Freshcells\SoapClientBundle\DataCollector;

use Symfony\Component\HttpKernel\DataCollector\DataCollector;

abstract class AbstractSoapDataCollector extends DataCollector
{
    private SoapCallRegistry $callRegistry;

    public function __construct(SoapCallRegistry $callRegistry)
    {
        $this->callRegistry = $callRegistry;
    }

    /**
     * @see collect()
     */
    protected function doCollect(): void
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

    public function reset(): void
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
    public function getName(): string
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
