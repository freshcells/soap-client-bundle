# Freshcells SoapClientBundle

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![soap-client-bundle](https://github.com/freshcells/soap-client-bundle/actions/workflows/test.yml/badge.svg)](https://github.com/freshcells/soap-client-bundle/actions/workflows/test.yml)
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]

Symfony integration for an advanced SoapClient.

![Profiler](/freshcells_soap_client_bundle_profiler.png?raw=true "Profiler Soap Client Bundle")

## Features
- Logging of requests, responses and faults
- XML can be anonymized before logging
- DataCollector and symfony profiler integration
- Events are dispatched before, after requests and in case of SoapFault
- Provide mock requests and responses while testing and developing

## Install

Via Composer

``` bash
$ composer require freshcells/soap-client-bundle
```

## Usage

Initalize the bundle:

    freshcells_soap_client:
      logger: monolog.logger.soap
      anonymize_logs: # in case you need to anonymize certain elements in the xml logs
        elements:
          - 'GivenName'
          - 'ns1:GivenName'
        attributes: [ 'PhoneNumber' ]
        namespaces:
          dummy: 'http://gcomputer.net/webservices/'
      truncate_element_logs: # in case you need to truncate some elements because values are too long for logging
        elements:
          - 'DailyDilbertResult'
        namespaces:
          dummy: 'http://gcomputer.net/webservices/'
        max_length: 2
      truncate_logs: # in case you need to truncate request/response logs
        max_length: 350

Create a Soap Client service and tag it with `freshcells_soap_client.client` 

    parameters:
        mock_requests:
            'http://gcomputer.net/webservices/DailyDilbert': './tests/Fixtures/MockRequest.xml'
        mock_responses:
            'http://gcomputer.net/webservices/DailyDilbert': './tests/Fixtures/MockResponse.xml'
        soap_options:
            mock_requests: '%mock_requests%'
            mock_responses: '%mock_responses%'
    services:
        _instanceof:
            Freshcells\SoapClientBundle\SoapClient\SoapClient:
                tags: [ 'freshcells_soap_client.client' ]

        Freshcells\SoapClientBundle\SoapClient\SoapClient:
            arguments: ['%soap_wsdl%', '%soap_options%']
            public: true


SoapClients are created outside of the bundle to give more flexibility, f.e when using generators like https://github.com/wsdl2phpgenerator/wsdl2phpgenerator.  
The bundle provides an advanced SoapClient that takes care of dispatching events, mocking and error handling.  
Make sure that you use this client or extend from it.


``` php
$response = $this->container->get('Freshcells\SoapClientBundle\SoapClient\SoapClient')->DailyDilbert();
```

**Note:**  
Mock requests can also be detected by a callable in case if Action and Location are not different by request, which is the case with some Soap servers.
See tests for implementation.


## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Testing

``` bash
$ composer test
```

## Todo
- add to timeline in profiler
- mock indicator
- make middlewares
- error / soap fault indicator in accordion header
- use options-resolver

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email ivo.bathke@freshcells.de instead of using the issue tracker.

## Credits

- [Freshcells engineering][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/freshcells/soap-client-bundle.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/freshcells/soap-client-bundle.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/freshcells/soap-client-bundle.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/freshcells/soap-client-bundle.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/freshcells/soap-client-bundle
[link-scrutinizer]: https://scrutinizer-ci.com/g/freshcells/soap-client-bundle/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/freshcells/soap-client-bundle
[link-downloads]: https://packagist.org/packages/freshcells/soap-client-bundle
[link-author]: https://github.com/freshcells
[link-contributors]: ../../contributors
