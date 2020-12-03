<?php

declare(strict_types=1);

namespace Communibase\Entity;

use Communibase\DataBag;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberUtil;

/**
 * @author Kingsquare (source@kingsquare.nl)
 * @copyright Copyright (c) Kingsquare BV (http://www.kingsquare.nl)
 */
class PhoneNumber
{
    /**
     * @var DataBag
     */
    protected $dataBag;

    /**
     * @var PhoneNumberUtil
     */
    private static $phoneNumberUtil;

    /**
     * @param array{'_id'?: string, 'type'?: string, 'areaCode'?: string|int, 'countryCode'?: string|int, 'subscriberNumber'?: string|int} $phoneNumberData
     */
    final private function __construct(array $phoneNumberData = [])
    {
        $this->dataBag = DataBag::create();
        if (empty($phoneNumberData['type'])) {
            $phoneNumberData['type'] = 'private';
        }
        $this->dataBag->addEntityData('phone', $phoneNumberData);
        self::$phoneNumberUtil = self::$phoneNumberUtil ?? PhoneNumberUtil::getInstance();
    }

    public static function create(): PhoneNumber
    {
        return new static();
    }

    /**
     * @param ?array{'_id'?: string, 'type'?: string, 'areaCode'?: string|int, 'countryCode'?: string|int, 'subscriberNumber'?: string|int} $phoneNumberData
     */
    public static function fromPhoneNumberData(array $phoneNumberData = null): PhoneNumber
    {
        return new static($phoneNumberData ?? []);
    }

    public static function fromString(string $phoneNumberString): PhoneNumber
    {
        $phoneNumber = static::create();
        $phoneNumber->setPhoneNumber($phoneNumberString);
        return $phoneNumber;
    }

    /**
     * @param string|null $format defaults to 'c(a)s'
     * The following characters are recognized in the format parameter string:
     * <table><tr>
     * <td>Character&nbsp;</td><td>Description</td>
     * </tr><tr>
     * <td>c</td><td>countryCode</td>
     * </tr><tr>
     * <td>a</td><td>areaCode</td>
     * </tr><tr>
     * <td>s</td><td>subscriberNumber</td>
     * </tr>
     */
    public function toString(?string $format = 'c (a) s'): string
    {
        if ($format === null) {
            $format = 'c (a) s';
        }
        $countryCode = $this->dataBag->get('phone.countryCode');
        $areaCode = $this->dataBag->get('phone.areaCode');
        $subscriberNumber = $this->dataBag->get('phone.subscriberNumber');
        if (!empty($countryCode) && strpos($countryCode, '+') !== 0) {
            $countryCode = '+' . $countryCode;
        }

        if (empty($areaCode) && empty($subscriberNumber)) {
            return '';
        }
        if (empty($areaCode)) {
            $areaCode = ''; // remove '0' values
            $format = (string)preg_replace('/\(\s?a\s?\)\s?/', '', $format);
        }
        if (!empty($countryCode) && strpos($format, 'c') !== false) {
            $areaCode = ltrim($areaCode, '0');
        }
        if (strpos($areaCode, '0') !== 0 && (empty($countryCode) || strpos($format, 'c') === false)) {
            $areaCode = '0' . $areaCode;
        }
        return trim(
            (string)preg_replace_callback(
                '![cas]!',
                static function (array $matches) use ($countryCode, $areaCode, $subscriberNumber) {
                    switch ($matches[0]) {
                        case 'c':
                            return $countryCode;
                        case 'a':
                            return $areaCode;
                        case 's':
                            return $subscriberNumber;
                    }
                    return '';
                },
                $format
            )
        );
    }

    public function __toString(): string
    {
        return $this->toString();
    }

    public function setPhoneNumber(?string $value): void
    {
        try {
            /** @var \libphonenumber\PhoneNumber $phoneNumber */
            $phoneNumber = self::$phoneNumberUtil->parse((string)$value, 'NL');
            $countryCode = (string)($phoneNumber->getCountryCode() ?? 0);
            $nationalNumber = (string)$phoneNumber->getNationalNumber();
            $split = preg_match('/^(1[035]|2[0346]|3[03568]|4[03568]|5[0358]|7\d)/', $nationalNumber) === 1 ? 2 : 3;
            if (strpos($nationalNumber, '6') === 0) {
                $split = 1;
            }
            $areaCode = substr($nationalNumber, 0, $split);
            $subscriberNumber = substr($nationalNumber, $split);
        } catch (NumberParseException $e) {
            $countryCode = '';
            $areaCode = '';
            $subscriberNumber = '';
        }
        $this->dataBag->set('phone.countryCode', $countryCode);
        $this->dataBag->set('phone.areaCode', $areaCode);
        $this->dataBag->set('phone.subscriberNumber', $subscriberNumber);
    }

    public function __clone()
    {
        $state = $this->getState();
        if ($state !== null) {
            unset($state['_id']);
            $this->dataBag->addEntityData('phone', $state);
        }
    }

    /**
     * @return ?array{'_id'?: string, 'type'?: string, 'areaCode'?: string|int, 'countryCode'?: string|int, 'subscriberNumber'?: string|int}
     */
    public function getState(): ?array
    {
        if (!array_filter([$this->dataBag->get('phone.areaCode'), $this->dataBag->get('phone.subscriberNumber')])) {
            return null;
        }
        return $this->dataBag->getState('phone');
    }
}
