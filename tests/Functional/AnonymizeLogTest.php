<?php

namespace Freshcells\SoapClientBundle\Tests\Functional;

use Freshcells\SoapClientBundle\SoapClient\SoapClient;
use Freshcells\SoapClientBundle\Tests\AnonymizeAppKernel;
use Gamez\Psr\Log\TestLogger;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AnonymizeLogTest extends WebTestCase
{
    protected static function getKernelClass()
    {
        return AnonymizeAppKernel::class;
    }

    protected function setUp()
    {
        parent::setUp();
        $this->client = static::createClient([
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

        $log = $container->get(TestLogger::class)->log;

        $this->assertCount(3, $log);
        $this->assertTrue(strpos($log[0]->message->value, '<ADate>*****</ADate>') !== false);
        $this->assertTrue(strpos($log[2]->message->value, '<DailyDilbertResult>*****</DailyDilbertResult>') !== false);

        $container->get('profiler')->get('freshcells_soap_client')->collect(new Request(), new Response());
        $this->assertEquals(1, $container->get('profiler')->get('freshcells_soap_client')->getTotal());
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        static::$class = null;
    }
}
