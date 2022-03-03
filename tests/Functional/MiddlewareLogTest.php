<?php

namespace Freshcells\SoapClientBundle\Tests\Functional;

use ColinODell\PsrTestLogger\TestLogger;
use Freshcells\SoapClientBundle\SoapClient\SoapClient;
use Freshcells\SoapClientBundle\Tests\MiddlewareAppKernel;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MiddlewareLogTest extends WebTestCase
{
    protected static function getKernelClass()
    {
        return MiddlewareAppKernel::class;
    }

    protected function setUp(): void
    {
        parent::setUp();
        static::createClient([
            'environment' => 'test',
            'debug'       => true,
        ]);
    }

    public function testBundle()
    {
        $container = static::$kernel->getContainer();

        $soapClient = $container->get(SoapClient::class);
        $this->assertInstanceOf(SoapClient::class, $soapClient);

        $response = $soapClient->DailyDilbert('heureka');

        $this->assertEquals('string', $response->DailyDilbertResult);

        $log = $container->get(TestLogger::class)->records;

        $this->assertCount(3, $log);
        $this->assertTrue(strpos($log[0]['message'], '<ADate>*****</ADate>') !== false);
        $this->assertTrue(strpos($log[2]['message'], '<DailyDilbertResult>st...</DailyDilbertResult>') !== false);
        $this->assertTrue(strlen($log[2]['message']) === 413); //truncated xml + log info

        $container->get('profiler')->get('freshcells_soap_client')->collect(new Request(), new Response());
        $this->assertEquals(1, $container->get('profiler')->get('freshcells_soap_client')->getTotal());
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        static::$class = null;
    }
}
