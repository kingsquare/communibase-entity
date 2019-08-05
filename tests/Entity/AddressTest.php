<?php

namespace Communibase\Tests\Entity;

use Communibase\Entity\Address;
use PHPUnit\Framework\TestCase;

/**
 * @author Kingsquare (source@kingsquare.nl)
 * @copyright Copyright (c) Kingsquare BV (http://www.kingsquare.nl)
 */
class AddressTest extends TestCase
{

    public function test_it_can_get_various_properties()
    {
        $data = [
            'property' => 'The White House',
            'street' => 'Zandvoortselaan',
            'streetNumber' => '185',
            'streetNumberAddition' => 'III',
            'zipcode' => '2042 XL',
            'city' => 'Zandvoort',
            'countryCode' => 'NL',
            'type' => 'work',
            '_id' => '5c3e042951f0be010443a1d2',
        ];
        $address = Address::fromAddressData($data);
        $this->assertSame($data['property'], $address->getProperty());
        $this->assertSame($data['street'], $address->getStreet());
        $this->assertSame($data['streetNumber'], $address->getStreetNumber());
        $this->assertSame($data['streetNumberAddition'], $address->getStreetNumberAddition());
        $this->assertSame($data['zipcode'], $address->getZipcode());
        $this->assertSame($data['city'], $address->getCity());
        $this->assertSame($data['type'], $address->getType());
        $this->assertSame($data['_id'], (string)$address->getId());
    }

    /**
     * @dataProvider setSetterProvider
     *
     * @param string $property
     * @param mixed $value
     */
    public function test_setters($property, $value)
    {
        $address = Address::fromAddressData();
        $address->{'set' . $property}($value);
        $this->assertSame($value, $address->{'get' . $property}());
    }

    public function setSetterProvider()
    {
        return [
            ['property', 'The White House'],
            ['street', 'Zandvoortselaan'],
            ['streetNumber', '185'],
            ['streetNumberAddition', 'III'],
            ['zipcode', '2042 XL'],
            ['city', 'Zandvoort'],
            ['countryCode', 'NL'],
            ['type', 'work'],
            ['type', 'work'],
        ];
    }

    public function test_can_set_geolocation()
    {
        $address = Address::fromAddressData();
        $lat = 52.36498073;
        $lng = 4.55567032;
        $address->setGeoLocation($lat, $lng);
        $this->assertSame([
            'lat' => $lat,
            'lng' => $lng,
        ], $address->getGeoLocation());
    }

    public function invalidGeolocationProvider()
    {
        return [
            [-91.0, 0.0],
            [91.0, 0.0],
            [0.0, -181.0],
            [0.0, 181.0],
        ];
    }

    /**
     * @dataProvider invalidGeolocationProvider
     * @expectedException \UnexpectedValueException
     *
     * @param float $latitude
     * @param float $longitude
     */
    public function test_we_cant_set_an_invalid_geolocation($latitude, $longitude)
    {
        $address = Address::fromAddressData();
        $address->setGeoLocation($latitude, $longitude);
    }

    public function test_it_can_be_stringed()
    {
        $data = [
            'property' => 'The White House',
            'street' => 'Zandvoortselaan',
            'streetNumber' => '185',
            'zipcode' => '2042 XL',
            'city' => 'Zandvoort',
            'countryCode' => 'NL',
            'type' => 'work',
            '_id' => '5c3e042951f0be010443a1d2',
        ];
        $address = Address::fromAddressData($data);
        $this->assertSame('Zandvoortselaan 185, 2042 XL, Zandvoort', $address->toString());
        $this->assertSame('Zandvoortselaan 185, 2042 XL, Zandvoort', (string)$address);
        $this->assertSame('Zandvoortselaan 185' . PHP_EOL . '2042 XL Zandvoort', $address->toString(false));
    }
    public function test_it_can_be_stringed_with_a_streetNumberAddition()
    {
        $data = [
            'property' => 'The White House',
            'street' => 'Zandvoortselaan',
            'streetNumber' => '185',
            'streetNumberAddition' => 'III',
            'zipcode' => '2042 XL',
            'city' => 'Zandvoort',
            'countryCode' => 'NL',
            'type' => 'work',
            '_id' => '5c3e042951f0be010443a1d2',
        ];
        $address = Address::fromAddressData($data);
        $this->assertSame('Zandvoortselaan 185 III, 2042 XL, Zandvoort', $address->toString());
        $this->assertSame('Zandvoortselaan 185 III, 2042 XL, Zandvoort', (string)$address);
        $this->assertSame('Zandvoortselaan 185 III' . PHP_EOL . '2042 XL Zandvoort', $address->toString(false));
    }

    public function test_it_can_be_empty()
    {
        $address = Address::fromAddressData();
        $this->assertNull($address->getState());
        $this->assertSame('', (string)$address);
    }

}
