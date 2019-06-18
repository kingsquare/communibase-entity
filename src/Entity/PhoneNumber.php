<?php

namespace Communibase\Entity;

use Communibase\DataBag;

/**
 * @author Kingsquare (source@kingsquare.nl)
 * @copyright Copyright (c) Kingsquare BV (http://www.kingsquare.nl)
 */
final class PhoneNumber
{
    /**
     * @var DataBag
     */
    private $dataBag;

    /**
     * PhoneNumber constructor.
     *
     * @param array $phoneNumberData
     */
    private function __construct(array $phoneNumberData)
    {
        $this->dataBag = DataBag::create();
        if (empty($phoneNumberData['type'])) {
            $phoneNumberData['type'] = 'private';
        }
        $this->dataBag->addEntityData('phone', $phoneNumberData);
    }

    /**
     * @param array $phoneNumberData
     *
     * @return self
     */
    public static function fromPhoneNumberData(array $phoneNumberData = null)
    {
        if ($phoneNumberData === null) {
            $phoneNumberData = [];
        }
        return new self($phoneNumberData);
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
     *
     * @return string
     */
    public function toString($format = null)
    {
        if ($format === null || !\is_string($format)) {
            $format = 'c (a) s';
        }
        $countryCode = $this->dataBag->get('phone.countryCode');
        $areaCode = $this->dataBag->get('phone.areaCode');
        $subscriberNumber = $this->dataBag->get('phone.subscriberNumber');
        if (empty($areaCode) && empty($subscriberNumber)) {
            return '';
        }
        if (empty($areaCode)) {
            $areaCode = ''; // remove '0' values
            $format = \preg_replace('/\(\s?a\s?\)\s?/', '', $format);
        }
        if (!empty($countryCode) && \strpos($format, 'c') !== false) {
            $areaCode = \ltrim($areaCode, '0');
        }
        return trim(
            \preg_replace_callback(
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

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->toString();
    }

    /**
     * @param string $value
     */
    public function setPhoneNumber($value)
    {
        $countryCode = '';
        preg_match('!^\+(\d+)(.+)!', $value, $matches);
        if (count($matches) === 3) {
            if (strlen($matches[2]) === 1) {
                $countryCode = substr($matches[1], 0, 2);
                $value = substr($matches[1], 2) . $matches[2];
            } else {
                $countryCode = $matches[1];
                $value = str_replace('(0)', '', (string)$matches[2]);
            }
        }
        $value = str_replace([') ', '-'], [')', ' '], $value);
        $value = (string)preg_replace('![^\d ]!', '', $value);
        $parts = explode(' ', trim($value));
        if (count($parts) > 1) {
            $areaCode = reset($parts);
            $subscriberNumber = implode('', array_slice($parts, 1));
        } else {
            $start = (!empty($value) && strpos($value, '06') === 0) ? 2 : 3;
            $areaCode = substr($value, 0, $start);
            $subscriberNumber = substr($value, $start);
        }

        $this->dataBag->set('phone.countryCode', $countryCode);
        $this->dataBag->set('phone.areaCode', $areaCode);
        $this->dataBag->set('phone.subscriberNumber', $subscriberNumber);
    }

    /**
     * @return array
     */
    public function getState()
    {
        if (!array_filter([$this->dataBag->get('phone.areaCode'), $this->dataBag->get('phone.subscriberNumber')])) {
            return null;
        }
        return $this->dataBag->getState('phone');
    }
}
