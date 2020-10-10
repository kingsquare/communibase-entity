<?php

declare(strict_types=1);

namespace Communibase\Tests\Entity;

use Communibase\Entity\Address;
use Communibase\Exception\InvalidGeoLocationException;
use PHPUnit\Framework\TestCase;

/**
 * @author Kingsquare (source@kingsquare.nl)
 * @copyright Copyright (c) Kingsquare BV (http://www.kingsquare.nl)
 */
class AddressTest extends TestCase
{

    public function test_it_can_get_various_properties(): void
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
        self::assertSame($data['property'], $address->getProperty());
        self::assertSame($data['street'], $address->getStreet());
        self::assertSame($data['streetNumber'], $address->getStreetNumber());
        self::assertSame($data['streetNumberAddition'], $address->getStreetNumberAddition());
        self::assertSame($data['zipcode'], $address->getZipcode());
        self::assertSame($data['city'], $address->getCity());
        self::assertSame($data['type'], $address->getType());
        self::assertSame($data['_id'], (string)$address->getId());
    }

    /**
     * @dataProvider setSetterProvider
     */
    public function test_setters(string $property, string $value): void
    {
        $address = Address::fromAddressData();
        $address->{'set' . $property}($value);
        self::assertSame($value, $address->{'get' . $property}());
    }

    public function setSetterProvider(): array
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
        ];
    }

    /**
     * @throws InvalidGeoLocationException
     */
    public function test_can_set_geolocation(): void
    {
        $address = Address::fromAddressData();
        $lat = 52.36498073;
        $lng = 4.55567032;
        $address->setGeoLocation($lat, $lng);
        self::assertSame(
            [
                'lat' => $lat,
                'lng' => $lng,
            ],
            $address->getGeoLocation()
        );
    }

    public function test_can_get_geolocation_using_point(): void
    {
        $lat = 52.36498073;
        $lng = 4.55567032;
        $address = Address::fromAddressData(
            [
                'point' => [
                    'coordinates' => [
                        0 => $lng,
                        1 => $lat,
                    ]
                ],
            ]
        );
        self::assertSame(
            [
                'lat' => $lat,
                'lng' => $lng,
            ],
            $address->getGeoLocation()
        );
    }

    public function test_can_get_geolocation_using_old_style(): void
    {
        // old style
        $lat = 52.36498073;
        $lng = 4.55567032;
        $address = Address::fromAddressData(
            [
                'latitude' => $lat,
                'longitude' => $lng,
            ]
        );
        self::assertSame(
            [
                'lat' => $lat,
                'lng' => $lng,
            ],
            $address->getGeoLocation()
        );
    }

    public function test_can_get_geolocation_prefers_point(): void
    {
        // old style
        $pointLat = 52.36498073;
        $pointLng = 4.55567032;
        $oldLat = 1.55567032;
        $oldLng = 55.55567032;
        $address = Address::fromAddressData(
            [
                'point' => [
                    'coordinates' => [
                        0 => $pointLng,
                        1 => $pointLat,
                    ]
                ],
                'latitude' => $oldLat,
                'longitude' => $oldLng,
            ]
        );
        self::assertSame(
            [
                'lat' => $pointLat,
                'lng' => $pointLng,
            ],
            $address->getGeoLocation()
        );
    }

    /**
     * @throws InvalidGeoLocationException
     */
    public function test_can_set_geolocation_using_point(): void
    {
        $newLat = 52.36498073;
        $newLng = 4.55567032;
        $oldLat = 1.55567032;
        $oldLng = 55.55567032;
        $address = Address::fromAddressData(
            [
                'point' => [
                    'coordinates' => [
                        0 => $oldLng,
                        1 => $oldLat,
                    ]
                ],
            ]
        );
        self::assertSame(
            [
                'lat' => $oldLat,
                'lng' => $oldLng,
            ],
            $address->getGeoLocation()
        );
        $address->setGeoLocation($newLat, $newLng);
        self::assertSame(
            [
                'lat' => $newLat,
                'lng' => $newLng,
            ],
            $address->getGeoLocation()
        );
    }

    public function invalidGeolocationProvider(): array
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
     *
     * @throws InvalidGeoLocationException
     */
    public function test_we_cant_set_an_invalid_geolocation(float $latitude, float $longitude): void
    {
        $this->expectException(InvalidGeoLocationException::class);
        $address = Address::fromAddressData();
        $address->setGeoLocation($latitude, $longitude);
    }

    public function test_it_can_be_stringed(): void
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
        self::assertSame('Zandvoortselaan 185, 2042 XL, Zandvoort', $address->toString());
        self::assertSame('Zandvoortselaan 185, 2042 XL, Zandvoort', (string)$address);
        self::assertSame('Zandvoortselaan 185' . PHP_EOL . '2042 XL Zandvoort', $address->toString(false));
    }

    public function test_it_can_be_stringed_with_a_streetNumberAddition(): void
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
        self::assertSame('Zandvoortselaan 185 III, 2042 XL, Zandvoort', $address->toString());
        self::assertSame('Zandvoortselaan 185 III, 2042 XL, Zandvoort', (string)$address);
        self::assertSame('Zandvoortselaan 185 III' . PHP_EOL . '2042 XL Zandvoort', $address->toString(false));
    }

    public function test_it_can_be_empty(): void
    {
        $address = Address::fromAddressData();
        self::assertNull($address->getState());
        self::assertSame('', (string)$address);
    }
}
