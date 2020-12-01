<?php

declare(strict_types=1);

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
final class Address
{
    /**
     * @var DataBag;
     */
    private $dataBag;

    private function __construct(array $addressData = [])
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
        return new static();
    }

    /**
     * @return static
     */
    public static function fromAddressData(array $addressData = null)
    {
        if ($addressData === null) {
            $addressData = [];
        }
        return new static($addressData);
    }

    public function getProperty(): string
    {
        return (string)$this->dataBag->get('address.property');
    }

    public function setProperty(?string $property): void
    {
        $this->dataBag->set('address.property', $property);
    }

    public function getStreet(): string
    {
        return trim((string)$this->dataBag->get('address.street'));
    }

    public function setStreet(?string $street): void
    {
        $this->dataBag->set('address.street', $street);
    }

    public function getStreetNumber(): string
    {
        return (string)$this->dataBag->get('address.streetNumber');
    }

    public function setStreetNumber(?string $streetNumber): void
    {
        $this->dataBag->set('address.streetNumber', $streetNumber);
    }

    public function getStreetNumberAddition(): string
    {
        return (string)$this->dataBag->get('address.streetNumberAddition');
    }

    public function setStreetNumberAddition(?string $streetNumberAddition): void
    {
        $this->dataBag->set('address.streetNumberAddition', $streetNumberAddition);
    }

    public function getZipcode(): string
    {
        return trim((string)$this->dataBag->get('address.zipcode'));
    }

    public function setZipcode(?string $zipcode): void
    {
        $this->dataBag->set('address.zipcode', $zipcode);
    }

    public function getCity(): string
    {
        return trim((string)$this->dataBag->get('address.city'));
    }

    public function setCity(?string $city): void
    {
        $this->dataBag->set('address.city', $city);
    }

    public function getCountryCode(string $default = 'NL'): string
    {
        return trim((string)$this->dataBag->get('address.countryCode', $default));
    }

    public function setCountryCode(string $countryCode): void
    {
        $this->dataBag->set('address.countryCode', $countryCode);
    }

    public function getType(): string
    {
        return trim((string)$this->dataBag->get('address.type'));
    }

    public function setType(?string $type): void
    {
        $this->dataBag->set('address.type', $type);
    }

    public function getId(): CommunibaseId
    {
        return CommunibaseId::fromString($this->dataBag->get('address._id'));
    }

    public function __toString()
    {
        return $this->toString();
    }

    public function toString(bool $singleLine = true): string
    {
        if ($this->getState() === null) {
            return '';
        }
        $lines = [
            array_filter([$this->getStreet(), $this->getStreetNumber(), $this->getStreetNumberAddition()]),
            array_filter([$this->getZipcode(), $this->getCity()]),
        ];

        if ($singleLine) {
            return implode(
                ', ',
                array_filter(
                    [
                        implode(' ', $lines[0]),
                        implode(', ', $lines[1]),
                    ]
                )
            );
        }
        return implode(
            PHP_EOL,
            array_filter(
                [
                    implode(' ', $lines[0]),
                    implode(' ', $lines[1]),
                ]
            )
        );
    }

    /**
     * @return float[]|null
     */
    public function getGeoLocation(): ?array
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
     * @throws InvalidGeoLocationException
     */
    public function setGeoLocation(float $latitude, float $longitude): void
    {
        $this->guardAgainstInvalidLatLong($latitude, $longitude);

        // native geo handling
        if ($this->isGeoStorageUsingNativePoint()) {
            $this->dataBag->set(
                'address.point',
                [
                    'coordinates' => [
                        0 => $longitude,
                        1 => $latitude,
                    ],
                ]
            );
            return;
        }
        $this->dataBag->set('address.latitude', $latitude);
        $this->dataBag->set('address.longitude', $longitude);
    }

    private function isGeoStorageUsingNativePoint(): bool
    {
        return !empty($this->dataBag->get('address.point'));
    }

    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        return empty(
        array_filter(
            [
                $this->getStreet(),
                $this->getStreetNumber(),
                $this->getZipcode(),
                $this->getCity()
            ]
        )
        );
    }

    public function __clone()
    {
        $state = $this->getState();
        if ($state !== null) {
            unset($state['_id']);
            $this->dataBag->addEntityData('address', $state);
        }
    }

    /**
     * @return array|null
     */
    public function getState(): ?array
    {
        if ($this->isEmpty()) {
            return null;
        }
        return $this->dataBag->getState('address');
    }

    /**
     * @throws InvalidGeoLocationException
     */
    private function guardAgainstInvalidLatLong(float $latitude, float $longitude): void
    {
        if ($latitude < -90 || $latitude > 90 || $longitude < -180 || $longitude > 180) {
            throw new InvalidGeoLocationException(
                sprintf('Invalid latitude/longitude: %s, %s', $latitude, $longitude)
            );
        }
    }
}
