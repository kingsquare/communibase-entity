<?php

namespace Communibase\Tests;

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
            ['+31251223344', '31', '251', '223344'], // #0
            ['+31(0)251-223344', '31', '251', '223344'], // #1
            ['+31(0) 251-223344', '31', '251', '223344'], // #2
            ['+31 (0) 251-223344', '31', '251', '223344'], // #3
            ['+31 (0) 251 22 33 44', '31', '251', '223344'], // #4
            ['+31 (5) 251 22 33 44', '31', '5251', '223344'], // #5
            ['+31 (0) 251 22 (33) 44', '31', '251', '223344'], // #6
            ['020-1234567', '', '020', '1234567'], // #7
            ['020 1234567', '', '020', '1234567'], // #8
            ['020 12 345 67', '', '020', '1234567'], // #9
            ['020 1 234 567', '', '020', '1234567'], // #10
            ['0201234567', '', '020', '1234567'], // #11
            ['06-12345678', '', '06', '12345678'], // #12
            ['+31 (0) 612345678', '31', '6', '12345678'], // #13
            ['0612345678', '', '06', '12345678'], // #14
            ['test', '', '', ''], // #15
            ['tel.:020-1234567', '', '020', '1234567'], // #16
            ['', '', '', ''], // #17
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
            ['', '06', '123456789', 'c (a) s', '(06) 123456789'], // #0
            ['+31', '06', '123456789', 'c (a) s', '+31 (6) 123456789'], // #1
            ['+31', '06', '123456789', '(a) s', '(06) 123456789'], // #2
            ['+31', '06', '123456789', 'cas', '+316123456789'], // #3
            ['+31', '06', '123456789', 'as', '06123456789'], // #4
            ['+31', '06', '123456789', 'a-s', '06-123456789'], // #5
            ['+31', '06', '123456789', null, '+31 (6) 123456789'], // #6
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
}
