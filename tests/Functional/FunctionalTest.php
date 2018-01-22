<?php

namespace Freshcells\SoapClientBundle\Tests\Functional;

use Freshcells\SoapClientBundle\SoapClient\SoapClient;
use Gamez\Psr\Log\TestLogger;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class FunctionalTest extends WebTestCase
{
    public function testBundle()
    {
        $client = static::createClient([
            'environment' => 'test',
            'debug'       => true,
        ]);

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
     * @expectedException Freshcells\SoapClientBundle\Exception\SoapException
     */
    public function testFault()
    {
        $client = static::createClient([
            'environment' => 'test',
            'debug'       => true,
        ]);

        $container = static::$kernel->getContainer();
        $soapClient = $container->get(SoapClient::class);

        try {
            $response = $soapClient->NoSuchAction('heureka');
        } catch (\Exception $e) {
            $log = $container->get(TestLogger::class)->log;
            $this->assertCount(1, $log);

            $container->get('profiler')->get('freshcells_soap_client')->collect(new Request(), new Response());
            $this->assertEquals(1, $container->get('profiler')->get('freshcells_soap_client')->getTotal());

            throw new $e;
        }
    }
}
