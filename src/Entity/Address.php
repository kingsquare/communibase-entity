<?php

namespace Communibase\Entity;

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
     * @param array|null $addressData
     */
    private function __construct(array $addressData = null)
    {
        $this->dataBag = DataBag::create();
        if ($addressData === null) {
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
     * @return CommunibaseId
     */
    public function getId()
    {
        return CommunibaseId::fromString($this->dataBag->get('address._id'));
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
        return implode($singleLine ? ', ' : PHP_EOL, array_filter([
            implode(' ', array_filter([$this->getStreet(), $this->getStreetNumber()])),
            implode(', ', array_filter([$this->getZipcode(), $this->getCity()]))
        ]));
    }

    /**
     * @return array
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
     * @return array
     */
    public function getState()
    {
        if ($this->isEmpty()) {
            return null;
        }
        return $this->dataBag->getState('address');
    }
}
