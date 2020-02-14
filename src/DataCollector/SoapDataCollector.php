<?php

namespace Freshcells\SoapClientBundle\DataCollector;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Kernel;

// @codingStandardsIgnoreStart
if (Kernel::MAJOR_VERSION >= 5) {
    class SoapDataCollector extends AbstractSoapDataCollector
    {
// @codingStandardsIgnoreEnd
        /**
         * {@inheritdoc}
         */
        public function collect(Request $request, Response $response, \Throwable $exception = null)
        {
            return $this->doCollect();
        }
    }
} else {
// @codingStandardsIgnoreStart
    class SoapDataCollector extends AbstractSoapDataCollector
    {
// @codingStandardsIgnoreEnd
        /**
         * {@inheritdoc}
         */
        public function collect(Request $request, Response $response, \Exception $exception = null)
        {
            return $this->doCollect();
        }
    }
}
