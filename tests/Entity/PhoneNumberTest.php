<?php

namespace Communibase\Entity\Tests;

use Communibase\Entity\PhoneNumber;
use PHPUnit\Framework\TestCase;

/**
 * @author Kingsquare (source@kingsquare.nl)
 * @copyright Copyright (c) Kingsquare BV (http://www.kingsquare.nl)
 */
class PhoneNumberTest extends TestCase
{
    /**
     * @return array
     */
    public function dataProvider()
    {
        return [
            'int. format' => ['+31251223344', '31', '251', '223344'],
            'int. format with (0)' => ['+31(0)251-223344', '31', '251', '223344'],
            'int. format with (0) + dashes' => ['+31(0) 251-223344', '31', '251', '223344'],
            'int. format with (0) + spaces + dashes' => ['+31 (0) 251-223344', '31', '251', '223344'],
            'int. format with (0) + spaces' => ['+31 (0) 251 22 33 44', '31', '251', '223344'],
            'int. format with (5)' => ['+31 (5) 251 22 33 44', '31', '5251', '223344'],
            'int. format with superfluous ( )' => ['+31 (0) 251 22 (33) 44', '31', '251', '223344'],
            'dutch with -' => ['020-1234567', '', '020', '1234567'],
            'dutch with space' => ['020 1234567', '', '020', '1234567'],
            'dutch with spaces' => ['020 12 345 67', '', '020', '1234567'],
            'dutch with more spaces' => ['020 1 234 567', '', '020', '1234567'],
            'dutch no space' => ['0201234567', '', '020', '1234567'],
            'dutch mobile with -' => ['06-12345678', '', '06', '12345678'],
            'int. format dutch mobile' => ['+31 (0) 612345678', '31', '6', '12345678'],
            'dutch mobile' => ['0612345678', '', '06', '12345678'],
            'invalid number' => ['test', '', '', ''],
            'prefixed number' => ['tel.:020-1234567', '', '020', '1234567'],
            'empty' => ['', '', '', ''],
        ];
    }

    /**
     * @dataProvider dataProvider
     *
     * @param string $value
     * @param string $countryCode
     * @param string $areaCode
     * @param string $subscriberNumber
     */
    public function testExplodePhoneNumber($value, $countryCode, $areaCode, $subscriberNumber)
    {
        $phoneNumber = PhoneNumber::fromPhoneNumberData();
        $phoneNumber->setPhoneNumber($value);
        $expected = empty($countryCode . $areaCode . $subscriberNumber)
            ? null
            : [
                'countryCode' => $countryCode,
                'areaCode' => $areaCode,
                'subscriberNumber' => $subscriberNumber,
                'type' => 'private',
            ];
        $this->assertEquals($expected, $phoneNumber->getState());
    }

    public function provider()
    {
        return [
            'c (a) s without countrycode' => ['', '06', '123456789', 'c (a) s', '(06) 123456789'],
            'c (a) s with countrycode' => ['+31', '06', '123456789', 'c (a) s', '+31 (6) 123456789'],
            '(a) s with countrycode' => ['+31', '06', '123456789', '(a) s', '(06) 123456789'],
            'cas with countrycode' => ['+31', '06', '123456789', 'cas', '+316123456789'],
            'as with countrycode' => ['+31', '06', '123456789', 'as', '06123456789'],
            'a-s with countrycode' => ['+31', '06', '123456789', 'a-s', '06-123456789'],
            'no format as international number' => ['+31', '06', '123456789', null, '+31 (6) 123456789'],
            'no format but with leading 0' => ['', '0', '06123456789', null, '06123456789'],
            'unknown format' => ['', '0', '06123456789', 'henk', 'henk'],
            'empty area and subscriber' => ['+31', '', '', '', ''],
        ];
    }

    /**
     * @dataProvider provider
     *
     * @param string $countryCode
     * @param string $areaCode
     * @param string $subscriberNumber
     * @param string $format
     * @param string $expected
     */
    public function test_to_string_is_correctly_formatted(
        $countryCode,
        $areaCode,
        $subscriberNumber,
        $format,
        $expected
    ) {
        $phoneNumber = PhoneNumber::fromPhoneNumberData([
            'countryCode' => $countryCode,
            'areaCode' => $areaCode,
            'subscriberNumber' => $subscriberNumber,
        ]);
        $this->assertSame($expected, $phoneNumber->toString($format));
    }

    public function test_it_can_be_cast_to_string()
    {
        $phoneNumber = PhoneNumber::fromPhoneNumberData([
            'countryCode' => '+31',
            'areaCode' => '06',
            'subscriberNumber' => '123456789',
        ]);
        $this->assertSame('+31 (6) 123456789', (string) $phoneNumber);
    }
}
