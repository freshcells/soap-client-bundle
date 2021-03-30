<?php

namespace Freshcells\SoapClientBundle\Tests\Functional;

use Freshcells\SoapClientBundle\SoapClient\SoapClient;
use Freshcells\SoapClientBundle\SoapClient\SoapClientInterface;
use Psr\Log\Test\TestLogger;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class FunctionalTest extends WebTestCase
{
    protected function setUp(): void
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

        $log = $container->get(TestLogger::class)->records;

        $this->assertCount(3, $log);

        $container->get('profiler')->get('freshcells_soap_client')->collect(new Request(), new Response());
        $this->assertEquals(1, $container->get('profiler')->get('freshcells_soap_client')->getTotal());
    }

    public function testLocalWsdl()
    {
        $this->markTestSkipped('Needs internet connection, just for demo purposes');
        $container = static::$kernel->getContainer();

        $soapClient = $container->get('soap_client_with local_wsdl');
        $response   = $soapClient->DailyDilbert('heureka');
        $this->assertTrue(isset($response->DailyDilbertResult));
    }

    public function testInterface()
    {
        $container = static::$kernel->getContainer();

        $soapClient = $container->get(SoapClient::class);
        $this->assertInstanceOf(SoapClientInterface::class, $soapClient);
    }

    /**
     * @dataProvider provideFaultyData
     */
    public function testFault($value, $count)
    {
        $this->expectException(\Exception::class);
        $container  = static::$kernel->getContainer();
        $soapClient = $container->get(SoapClient::class);

        try {
            $response = $soapClient->NoSuchAction($value);
        } catch (\Exception $e) {
            $log = $container->get(TestLogger::class)->records;
            $this->assertCount($count, $log);

            $container->get('profiler')->get('freshcells_soap_client')->collect(new Request(), new Response());
            $this->assertEquals(1, $container->get('profiler')->get('freshcells_soap_client')->getTotal());

            throw $e;
        }
    }

    public function provideFaultyData()
    {
        return [
            ['heureka', 1],
            [['val' => 'heureka'], 1],
        ];
    }

    public function testMockDetector()
    {
        $container  = static::$kernel->getContainer();
        $soapClient = $container->get('soap_client_with mock_detector');
        $response   = $soapClient->DailyDilbert('heureka');

        $this->assertEquals('string', $response->DailyDilbertResult);
    }

    public function testDefaultOptions()
    {
        $expectedOptions = [
            // default options
            'compression'        => (SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP),
            'cache_wsdl'         => WSDL_CACHE_BOTH,
            'connection_timeout' => 60,
            'exceptions'         => true,
            'features'           => SOAP_SINGLE_ELEMENT_ARRAYS,
            'soap_version'       => SOAP_1_2,
            'trace'              => true,
            'user_agent'         => 'freshcells/soap-client-bundle',
        ];

        $container = static::$kernel->getContainer();

        $soapClient = $container->get('soap_client_without_custom_options');

        $this->assertEquals($expectedOptions, $soapClient->getOptions());
    }

    public function testCustomOptions()
    {
        $expectedOptions = [
            'compression'        => (SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP),
            'cache_wsdl'         => WSDL_CACHE_BOTH,
            'connection_timeout' => 5,
            'exceptions'         => true,
            'features'           => SOAP_SINGLE_ELEMENT_ARRAYS,
            'soap_version'       => SOAP_1_1,
            'trace'              => false,
            'user_agent'         => 'freshcells/soap-client-bundle',
        ];

        $container = static::$kernel->getContainer();

        $soapClient = $container->get('soap_client_with_custom_options');

        $this->assertEquals($expectedOptions, $soapClient->getOptions());
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        static::$class = null;
    }
}
