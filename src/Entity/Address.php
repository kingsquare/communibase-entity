<?php

namespace Communibase\Entity;

use Communibase\CommunibaseId;
use Communibase\DataBag;

/**
 * Communibase Address
 *
 * @author Kingsquare (source@kingsquare.nl)
 * @copyright Copyright (c) Kingsquare BV (http://www.kingsquare.nl)
 */
final class Address
{
    /**
     * @var DataBag;
     */
    private $dataBag;

    /**
     * @param array $addressData
     */
    private function __construct(array $addressData)
    {
        $this->dataBag = DataBag::create();
        if ($addressData === []) {
            return;
        }
        $this->dataBag->addEntityData('address', $addressData);
    }

    /**
     * @param array|null $addressData
     *
     * @return Address
     */
    public static function fromAddressData(array $addressData = null)
    {
        if ($addressData === null) {
            $addressData = [];
        }
        return new self($addressData);
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
            array_filter([$this->getStreet(), $this->getStreetNumber()]),
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
     * @return array|null
     */
    public function getGeoLocation()
    {
        $lat = $this->dataBag->get('address.latitude');
        $lng = $this->dataBag->get('address.longitude');
        if (!isset($lat, $lng)) {
            return null;
        }
        return [
            'lat' => $lat,
            'lng' => $lng,
        ];
    }

    /**
     * @param string|float $latitude
     * @param string|float $longitude
     */
    public function setGeoLocation($latitude, $longitude)
    {
        $this->dataBag->set('address.latitude', $latitude);
        $this->dataBag->set('address.longitude', $longitude);
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
}
