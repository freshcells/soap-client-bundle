<?php

namespace Freshcells\SoapClientBundle\Tests\Functional;

use Freshcells\SoapClientBundle\SoapClient\SoapClient;
use Gamez\Psr\Log\TestLogger;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class FunctionalTest extends WebTestCase
{
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

        $container->get('profiler')->get('freshcells_soap_client')->collect(new Request(), new Response());
        $this->assertEquals(1, $container->get('profiler')->get('freshcells_soap_client')->getTotal());
    }

    /**
     * @expectedException \Exception
     */
    public function testFault()
    {
        $container = static::$kernel->getContainer();
        $soapClient = $container->get(SoapClient::class);

        try {
            $response = $soapClient->NoSuchAction('heureka');
        } catch (\Exception $e) {
            $log = $container->get(TestLogger::class)->log;
            $this->assertCount(1, $log);

            $container->get('profiler')->get('freshcells_soap_client')->collect(new Request(), new Response());
            $this->assertEquals(1, $container->get('profiler')->get('freshcells_soap_client')->getTotal());

            throw $e;
        }
    }

    public function testMockDetector()
    {
        $container = static::$kernel->getContainer();
        $soapClient = $container->get('soap_client_with mock_detector');
        $response = $soapClient->DailyDilbert('heureka');

        $this->assertEquals('string', $response->DailyDilbertResult);
    }
}
