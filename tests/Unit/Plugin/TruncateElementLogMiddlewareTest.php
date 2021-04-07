<?php

namespace Freshcells\SoapClientBundle\Tests\Unit\Plugin;

use Freshcells\SoapClientBundle\Plugin\TruncateElementLogMiddleware;
use PHPUnit\Framework\TestCase;

class TruncateElementLogMiddlewareTest extends TestCase
{
    public function testTruncate()
    {
        $middleware = new TruncateElementLogMiddleware(
            ['dummy:Text'],
            ['dummy' => 'http://gcomputer.net/webservices/'],
            10
        );
        $res        = $middleware->apply($this->getXml());
        $this->assertTrue(strpos($res, '<Text>Powder ca...</Text>') !== false);
    }

    protected function getXml()
    {
        return '<?xml version="1.0" encoding="utf-8"?>
<soap12:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap12="http://www.w3.org/2003/05/soap-envelope">
    <soap12:Body>
        <DailyDilbert xmlns="http://gcomputer.net/webservices/">
            <ADate>dateTime</ADate>
            <Text>
Powder carrot cake jelly beans cake danish halvah. Marzipan sugar plum I love sugar plum soufflé dragée gummies jelly-o cupcake. Muffin donut donut icing fruitcake I love caramels biscuit tootsie roll.

Sweet sesame snaps biscuit tootsie roll sweet dragée chocolate bar chocolate. Ice cream bear claw chocolate bar I love jujubes pastry I love jelly beans carrot cake. Sesame snaps halvah pie tiramisu ice cream jelly beans cake macaroon jujubes.

Gummies I love candy jelly beans. Toffee oat cake I love icing topping. Toffee jujubes tart halvah lollipop cake wafer I love lemon drops. Cotton candy danish tiramisu I love oat cake brownie marshmallow chocolate cake.

Ice cream pastry powder. Bonbon dessert chocolate bar icing. Gingerbread chocolate cake ice cream caramels.

Lemon drops marshmallow chocolate. Soufflé I love gummi bears icing chocolate cake sweet roll icing. Candy chupa chups chocolate bar.
            </Text>
        </DailyDilbert>
    </soap12:Body>
</soap12:Envelope>
';
    }
}
