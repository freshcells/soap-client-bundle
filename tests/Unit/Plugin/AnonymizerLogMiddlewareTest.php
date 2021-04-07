<?php

namespace Freshcells\SoapClientBundle\Tests\Unit\Plugin;

use Freshcells\SoapClientBundle\Plugin\AnonymizerLogMiddleware;
use PHPUnit\Framework\TestCase;

class AnonymizerLogMiddlewareTest extends TestCase
{
    public function testApply()
    {
        $middleware = new AnonymizerLogMiddleware(
            ['dummy:ADate'],
            ['dummy:ADate[@time]'],
            '*****',
            ['dummy' => 'http://gcomputer.net/webservices/']
        );
        $res        = $middleware->apply($this->getXml());
        $this->assertTrue(strpos($res, '<ADate time="*****">*****</ADate>') !== false);
    }

    protected function getXml()
    {
        return '<?xml version="1.0" encoding="utf-8"?>
<soap12:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap12="http://www.w3.org/2003/05/soap-envelope">
    <soap12:Body>
        <DailyDilbert xmlns="http://gcomputer.net/webservices/">
            <ADate time="XXX">dateTime</ADate>
        </DailyDilbert>
    </soap12:Body>
</soap12:Envelope>
';
    }
}
