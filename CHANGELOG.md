# Changelog

All notable changes to `soap-client-bundle` will be documented in this file.

Updates should follow the [Keep a CHANGELOG](http://keepachangelog.com/) principles.

### 2.1.0
  - PHP8
### 2.0.0
  - bumbed PHP version to 7.4
  - ditched symfony 3.4 support
  - added Log Middleware
  - added TruncateElementLogMiddleware
  - added TruncateLogMiddleware
  - moved AnonymizerLogPlugin to AnonymizerLogMiddleware
  - removed Gamez/TestLogger
### 1.8.0
  - added AnonymizerLogPlugin
### 1.7.0  
  - added download for requests in profiler
  - fixed fault handler
  - use array short notation
  - improved LegacyEventDispatcherProxy usage
### 1.6.0  
  - removed location resolving from wsdl  
  - fixed dark-theme
### 1.5.0  
  - symfony5 compat  
  - dropped PHP7.1 support
### 1.4.0
  - introduced getOptions method to retrieve all SoapClient options (default + custom)
  - fixed null handling in ResponseEvent & pre- and postCall
### 1.3.0
  - introduced interface for SoapClient
  - upgrade phpunit to v7
### 1.2.0
  - introduced enable_profiler config option to improve prod settings
### 1.1.0
  - introduced the possibility to add callables as Mock detectors
  - made addRequest, addResponse public therefor
