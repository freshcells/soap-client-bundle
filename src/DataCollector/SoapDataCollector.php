<?php

namespace Freshcells\SoapClientBundle\DataCollector;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Kernel;

if (Kernel::MAJOR_VERSION >= 5) {
    class SoapDataCollector extends AbstractSoapDataCollector
    {
        /**
         * {@inheritdoc}
         */
        public function collect(Request $request, Response $response, \Throwable $exception = null)
        {
            return $this->doCollect();
        }
    }
} else {
    class SoapDataCollector extends AbstractSoapDataCollector
    {
        /**
         * {@inheritdoc}
         */
        public function collect(Request $request, Response $response, \Exception $exception = null)
        {
            return $this->doCollect();
        }
    }
}
