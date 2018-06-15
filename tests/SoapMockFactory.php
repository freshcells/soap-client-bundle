<?php

namespace Freshcells\SoapClientBundle\Tests;

class SoapMockFactory
{
    public static function createMockResponseDetector(): callable
    {
        return function ($request, $location, $action, $version, $one_way) {
            if(strpos($request, '<DailyDilbert') !== false){
                return __DIR__.'/Fixtures/MockResponse.xml';
            }

            return false;
        };
    }
}
