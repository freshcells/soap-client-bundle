<?php

namespace Freshcells\SoapClientBundle\SoapClient;

use Freshcells\SoapClientBundle\Event\Events;
use Freshcells\SoapClientBundle\Event\FaultEvent;
use Freshcells\SoapClientBundle\Event\RequestEvent;
use Freshcells\SoapClientBundle\Event\ResponseEvent;
use Freshcells\SoapClientBundle\Exception\SoapException;
use Ramsey\Uuid\Uuid;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class SoapClient
 */
class SoapClient extends \SoapClient
{
    /**
     * @var array
     */
    protected $options;
    /**
     * @var EventDispatcherInterface
     */
    protected $dispatcher;
    /**
     * @var array
     */
    private $mockRequests = [];
    /**
     * @var array
     */
    private $mockResponses = [];

    /**
     * SoapClient constructor.
     * @param null $wsdl
     * @param array|null $options
     */
    public function __construct($wsdl = null, array $options = [])
    {
        $defaults = array(
            'compression'        => (SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP),
            'cache_wsdl'         => WSDL_CACHE_NONE,
            'connection_timeout' => 60,
            'exceptions'         => true,
            'features'           => SOAP_SINGLE_ELEMENT_ARRAYS,
            'soap_version'       => SOAP_1_2,
            'trace'              => true,
            'user_agent'         => 'freshcells/soap-client-bundle',
        );

        $options = array_merge($defaults, $options);

        if (!is_null($wsdl)) {
            // if option 'location' not set explicit use WSDL URL as service location
            if (!isset($options['location'])) {
                $options['location'] = $this->resolveLocation($wsdl);
            }
        }

        $this->SoapClient($wsdl, $options);
        $this->options = $options;
    }

    public function __call($function_name, $arguments)
    {
        try {
            $response = parent::__call($function_name, $arguments);
            //works only with 'exceptions' => false
            if (is_soap_fault($response)) {
                throw $response;
            }
        } catch (\Exception $e) {
            $soapException = SoapException::fromThrowable($e);

            $request = $this->__getLastRequest();
            if ($request === null) {
                $request = implode(' ', $arguments);
            }
            $id = Uuid::uuid1();
            $this->faultCall($id->toString(), $function_name, $request, $soapException);

            throw $soapException;
        }

        return $response;
    }

    /**
     * @param string $request
     * @param string $location
     * @param string $action
     * @param int $version
     * @param null $one_way
     * @return bool|string
     */
    public function __doRequest($request, $location, $action, $version, $one_way = null)
    {
        $id = Uuid::uuid1();

        foreach ($this->mockRequests as $key => $mockRequest) {
            if (strrpos($action, $key) !== false) {
                $request = file_get_contents($mockRequest);
                break;
            }
        }

        $this->preCall($id->toString(), $action, $request);

        foreach ($this->mockResponses as $key => $mockResponse) {
            if (strrpos($action, $key) !== false) {
                $response = file_get_contents($mockResponse);

                $this->postCall($id->toString(), $action, $response);

                return $response;
            }
        }

        /* workaround for working timeout */
        $socketTimeout = false;
        if (isset($this->options['connection_timeout'])
            && (int)$this->options['connection_timeout'] > (int)ini_get('default_socket_timeout')
        ) {
            $socketTimeout = ini_set('default_socket_timeout', $this->options['connection_timeout']);
        }

        $response = parent::__doRequest($request, $location, $action, $version, $one_way);

        $this->postCall($id->toString(), $action, $response);

        if ($socketTimeout !== false) {
            ini_set('default_socket_timeout', $socketTimeout);
        }

        return $response;
    }

    /**
     * Triggered before a request is executed
     *
     * @param string $id
     * @param string $resource
     * @param string $requestContent
     */
    protected function preCall(string $id, string $resource, string $requestContent = null)
    {
        if (null !== $this->dispatcher) {
            $this->dispatcher->dispatch(Events::REQUEST, new RequestEvent($id, $resource, $requestContent));
        }
    }

    /**
     * @param string $id
     * @param string $resource
     * @param string $response
     */
    protected function postCall(string $id, string $resource, string $response = null)
    {
        if (null !== $this->dispatcher) {
            $responseEvent = new ResponseEvent(
                $id,
                $resource,
                $this->__getLastRequest(),
                $this->__getLastRequestHeaders(),
                $response,
                $this->__getLastResponseHeaders()
            );
            $this->dispatcher->dispatch(Events::RESPONSE, $responseEvent);
        }
    }

    /**
     * @param string $id
     * @param $resource
     * @param $requestContent
     * @param $soapException
     */
    protected function faultCall(string $id, string $resource, string $requestContent, SoapException $soapException)
    {
        if (null !== $this->dispatcher) {
            $this->dispatcher->dispatch(
                Events::FAULT,
                new FaultEvent($id, $soapException, new RequestEvent($id, $resource, $requestContent))
            );
        }
    }

    /**
     * @param string $wsdl
     * @return string
     */
    protected function resolveLocation($wsdl)
    {
        $wsdlUrl = parse_url($wsdl);

        return ((isset($wsdlUrl['scheme'])) ? $wsdlUrl['scheme'].'://' : '')
            .((isset($wsdlUrl['user'])) ? $wsdlUrl['user']
                .((isset($wsdlUrl['pass'])) ? ':'.$wsdlUrl['pass'] : '').'@' : '')
            .((isset($wsdlUrl['host'])) ? $wsdlUrl['host'] : '')
            .((isset($wsdlUrl['port'])) ? ':'.$wsdlUrl['port'] : '')
            .((isset($wsdlUrl['path'])) ? $wsdlUrl['path'] : '');
    }

    /**
     * @param array $mockRequests
     */
    public function setMockRequests(array $mockRequests)
    {
        $this->mockRequests = $mockRequests;
    }

    /**
     * @param array $mockResponses
     */
    public function setMockResponses(array $mockResponses)
    {
        $this->mockResponses = $mockResponses;
    }

    /**
     * @param EventDispatcherInterface $dispatcher
     */
    public function setDispatcher(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }
}
