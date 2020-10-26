<?php

namespace Freshcells\SoapClientBundle\SoapClient;

use Freshcells\SoapClientBundle\Event\Event;
use Freshcells\SoapClientBundle\Event\Events;
use Freshcells\SoapClientBundle\Event\FaultEvent;
use Freshcells\SoapClientBundle\Event\RequestEvent;
use Freshcells\SoapClientBundle\Event\ResponseEvent;
use Ramsey\Uuid\Uuid;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\Kernel;

/**
 * Class SoapClient
 */
class SoapClient extends \SoapClient implements SoapClientInterface
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

        if (isset($options['mock_requests'])) {
            $this->setMockRequests($options['mock_requests']);
            unset($options['mock_requests']);
        }
        if (isset($options['mock_responses'])) {
            $this->setMockResponses($options['mock_responses']);
            unset($options['mock_responses']);
        }

        $defaults = [
            'compression'        => (SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP),
            'cache_wsdl'         => WSDL_CACHE_BOTH,
            'connection_timeout' => 60,
            'exceptions'         => true,
            'features'           => SOAP_SINGLE_ELEMENT_ARRAYS,
            'soap_version'       => SOAP_1_2,
            'trace'              => true,
            'user_agent'         => 'freshcells/soap-client-bundle',
        ];

        $options = array_merge($defaults, $options);

        $this->SoapClient($wsdl, $options);
        $this->options = $options;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function __call($function_name, $arguments)
    {
        try {
            $response = parent::__call($function_name, $arguments);
            //works only with 'exceptions' => false, we always throw
            if (is_soap_fault($response)) {
                throw $response;
            }
        } catch (\Exception $e) {
            $this->handleFault($function_name, $arguments, $e);
        }

        return $response;
    }

    public function __soapCall(
        $function_name,
        $arguments,
        $options = null,
        $input_headers = null,
        &$output_headers = null
    ) {
        try {
            $response = parent::__soapCall($function_name, $arguments, $options, $input_headers, $output_headers);
            //works only with 'exceptions' => false, we always throw
            if (is_soap_fault($response)) {
                throw $response;
            }
        } catch (\Exception $e) {
            $this->handleFault($function_name, $arguments, $e);
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
            if (is_string($key)) {
                if (strrpos($action, $key) !== false) {
                    $request = file_get_contents($mockRequest);
                    break;
                }
            } else {
                if (is_callable($mockRequest)) {
                    if ($requestFilePath = $mockRequest($request, $location, $action, $version, $one_way)) {
                        $request = file_get_contents($requestFilePath);
                        break;
                    }
                }
            }
        }

        $this->preCall($id->toString(), (string)$action, $request);

        foreach ($this->mockResponses as $key => $mockResponse) {
            if (is_string($key)) {
                if (strrpos($action, $key) !== false) {
                    $response = file_get_contents($mockResponse);

                    $this->postCall($id->toString(), $action, $response);

                    return $response;
                }
            } else {
                if (is_callable($mockResponse)) {
                    if ($responseFilePath = $mockResponse($request, $location, $action, $version, $one_way)) {
                        $response = file_get_contents($responseFilePath);

                        $this->postCall($id->toString(), $action, $response);

                        return $response;
                    }
                }
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

        $this->postCall($id->toString(), (string)$action, $response);

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
        $this->dispatch(new RequestEvent($id, $resource, $requestContent), Events::REQUEST);
    }

    /**
     * @param string $id
     * @param string $resource
     * @param string $response
     */
    protected function postCall(string $id, string $resource, string $response = null)
    {
        $responseEvent = new ResponseEvent(
            $id,
            $resource,
            $this->__getLastRequest(),
            $this->__getLastRequestHeaders(),
            $response,
            $this->__getLastResponseHeaders()
        );
        $this->dispatch($responseEvent, Events::RESPONSE);
    }

    /**
     * @param string $id
     * @param string $resource
     * @param string $requestContent
     * @param \Exception $exception
     */
    protected function faultCall(string $id, string $resource, string $requestContent, \Exception $exception)
    {
        $this->dispatch(
            new FaultEvent($id, $exception, new RequestEvent($id, $resource, $requestContent)),
            Events::FAULT
        );
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
     * @required
     */
    public function setDispatcher(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param $function_name
     * @param $arguments
     * @param $e
     */
    protected function handleFault($function_name, $arguments, $e): void
    {
        $request = $this->__getLastRequest();
        if ($request === null) { //only dispatch this when no request was fired
            $request = print_r($arguments, true);
            $id      = Uuid::uuid1();
            $this->faultCall($id->toString(), $function_name, $request, $e);
        }

        throw $e;
    }

    /**
     * @param Event $event
     * @param string $eventName
     */
    private function dispatch(Event $event, $eventName)
    {
        if (null === $this->dispatcher) {
            return;
        }

        // EventDispatcher signature changed in Symfony 4.3
        if (Kernel::VERSION_ID < 40300) {
            // Old EventDispatcher signature
            $this->dispatcher->dispatch($eventName, $event);
        } else {
            // New Symfony 4.3 EventDispatcher signature
            $this->dispatcher->dispatch($event, $eventName);
        }
    }
}
