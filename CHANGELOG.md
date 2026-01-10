# Changelog

All notable changes to `soap-client-bundle` will be documented in this file.

Updates should follow the [Keep a CHANGELOG](http://keepachangelog.com/) principles.

### 3.1.0
 - remove unsupported php versions 8.1
 - CI improvements: update github actions, add debug-bundle constraint to fix dependencies
### 3.0.0
  - Drop unsupported php & symfony versions:  
      - only php 8.1, 8.2, 8.3 see https://www.php.net/supported-versions.php
      - only symfony 5.4, 6.4, 7.0 see https://symfony.com/releases
      - update phpunit to support coverage for php8
      - use matrix for github actions
      - exclude php 8.1 & symfony 7 because symfony 7 requires php >=8.2
      - fix deprecations
      - remove bc layers
      - replace required annotation by attribute
### 2.3.2
  - added scroll fix
### 2.3.1
  - fixes return types
### 2.3.0
  - symfony6 compat
### 2.2.1
  - fixed psr/log dep versions
### 2.2.0
  - return types
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
