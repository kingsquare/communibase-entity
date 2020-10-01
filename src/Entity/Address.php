<?php

namespace Communibase\Entity;

use Communibase\CommunibaseId;
use Communibase\DataBag;
use Communibase\Exception\InvalidGeoLocationException;

/**
 * Communibase Address
 *
 * @author Kingsquare (source@kingsquare.nl)
 * @copyright Copyright (c) Kingsquare BV (http://www.kingsquare.nl)
 */
class Address
{
    /**
     * @var DataBag;
     */
    protected $dataBag;

    /**
     * @param array $addressData
     */
    protected function __construct(array $addressData)
    {
        $this->dataBag = DataBag::create();
        if ($addressData === []) {
            return;
        }
        $this->dataBag->addEntityData('address', $addressData);
    }

    /**
     * @return static
     */
    public static function create()
    {
        return new static([]);
    }

    /**
     * @param array|null $addressData
     *
     * @return static
     */
    public static function fromAddressData(array $addressData = null)
    {
        if ($addressData === null) {
            $addressData = [];
        }
        return new static($addressData);
    }

    /**
     * @return string
     */
    public function getProperty()
    {
        return $this->dataBag->get('address.property');
    }

    /**
     * @param string $property
     */
    public function setProperty($property)
    {
        $this->dataBag->set('address.property', $property);
    }

    /**
     * @return string
     */
    public function getStreet()
    {
        return trim((string)$this->dataBag->get('address.street'));
    }

    /**
     * @param string $street
     */
    public function setStreet($street)
    {
        $this->dataBag->set('address.street', (string)$street);
    }

    /**
     * @return string
     */
    public function getStreetNumber()
    {
        return $this->dataBag->get('address.streetNumber');
    }

    /**
     * @param string $streetNumber
     */
    public function setStreetNumber($streetNumber)
    {
        $this->dataBag->set('address.streetNumber', (string)$streetNumber);
    }

    /**
     * @return string
     */
    public function getStreetNumberAddition()
    {
        return $this->dataBag->get('address.streetNumberAddition');
    }

    /**
     * @param string $streetNumberAddition
     */
    public function setStreetNumberAddition($streetNumberAddition)
    {
        $this->dataBag->set('address.streetNumberAddition', (string)$streetNumberAddition);
    }

    /**
     * @return string
     */
    public function getZipcode()
    {
        return trim((string)$this->dataBag->get('address.zipcode'));
    }

    /**
     * @param string $zipcode
     */
    public function setZipcode($zipcode)
    {
        $this->dataBag->set('address.zipcode', (string)$zipcode);
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return trim((string)$this->dataBag->get('address.city'));
    }

    /**
     * @param string $city
     */
    public function setCity($city)
    {
        $this->dataBag->set('address.city', (string)$city);
    }

    /**
     * @param string $default
     *
     * @return string
     */
    public function getCountryCode($default = 'NL')
    {
        return trim((string)$this->dataBag->get('address.countryCode', $default));
    }

    /**
     * @param string $countryCode
     */
    public function setCountryCode($countryCode)
    {
        $this->dataBag->set('address.countryCode', (string)$countryCode);
    }

    /**
     * @return string
     */
    public function getType()
    {
        return trim((string)$this->dataBag->get('address.type'));
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->dataBag->set('address.type', (string)$type);
    }

    /**
     * @return CommunibaseId
     */
    public function getId()
    {
        return CommunibaseId::fromString($this->dataBag->get('address._id'));
    }

    public function __toString()
    {
        return $this->toString();
    }

    /**
     * @param bool $singleLine
     *
     * @return string
     */
    public function toString($singleLine = true)
    {
        if ($this->getState() === null) {
            return '';
        }
        $lines = [
            array_filter([$this->getStreet(), $this->getStreetNumber(), $this->getStreetNumberAddition()]),
            array_filter([$this->getZipcode(), $this->getCity()]),
        ];

        if ($singleLine) {
            return implode(', ', array_filter([
                implode(' ', $lines[0]),
                implode(', ', $lines[1]),
            ]));
        }
        return implode(PHP_EOL, array_filter([
            implode(' ', $lines[0]),
            implode(' ', $lines[1]),
        ]));
    }

    /**
     * @return float[]|null
     */
    public function getGeoLocation()
    {
        // native geo handling
        if ($this->isGeoStorageUsingNativePoint()) {
            $point = $this->dataBag->get('address.point');
            if (empty($point) || empty($point['coordinates']) || empty($point['coordinates'][0]) || empty($point['coordinates'][1])) {
                return null;
            }
            return [
                'lat' => (float)$point['coordinates'][1],
                'lng' => (float)$point['coordinates'][0],
            ];
        }

        // `old`-style
        $lat = $this->dataBag->get('address.latitude');
        $lng = $this->dataBag->get('address.longitude');
        if (!isset($lat, $lng)) {
            return null;
        }
        return [
            'lat' => (float)$lat,
            'lng' => (float)$lng,
        ];
    }

    /**
     * @param float $latitude
     * @param float $longitude
     * @throws InvalidGeoLocationException
     */
    public function setGeoLocation($latitude, $longitude)
    {
        $latitude = (float)$latitude;
        $longitude = (float)$longitude;
        $this->guardAgainstInvalidLatLong($latitude, $longitude);

        // native geo handling
        if ($this->isGeoStorageUsingNativePoint()) {
            $this->dataBag->set('address.point', [
                'coordinates' => [
                    0 => $longitude,
                    1 => $latitude,
                ],
            ]);
            return;
        }
        $this->dataBag->set('address.latitude', $latitude);
        $this->dataBag->set('address.longitude', $longitude);
    }

    private function isGeoStorageUsingNativePoint()
    {
        return !empty($this->dataBag->get('address.point'));
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return empty(array_filter([
            $this->getStreet(),
            $this->getStreetNumber(),
            $this->getZipcode(),
            $this->getCity()
        ]));
    }

    /**
     * @return array|null
     */
    public function getState()
    {
        if ($this->isEmpty()) {
            return null;
        }
        return $this->dataBag->getState('address');
    }

    /**
     * @param float $latitude
     * @param float $longitude
     * @throws InvalidGeoLocationException
     */
    protected function guardAgainstInvalidLatLong($latitude, $longitude)
    {
        if ($latitude < -90 || $latitude > 90 || $longitude < -180 || $longitude > 180) {
            throw new InvalidGeoLocationException(\sprintf('Invalid latitude/longitude: %s, %s', $latitude, $longitude));
        }
    }
}
