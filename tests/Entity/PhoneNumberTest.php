<?php

declare(strict_types=1);

namespace Communibase\Tests\Entity;

use Communibase\Entity\PhoneNumber;
use PHPUnit\Framework\TestCase;

/**
 * @author Kingsquare (source@kingsquare.nl)
 * @copyright Copyright (c) Kingsquare BV (http://www.kingsquare.nl)
 */
class PhoneNumberTest extends TestCase
{
    /**
     * @return array<string, array<string>>
     */
    public function dataProvider(): array
    {
        return [
            'int. format' => ['+31251223344', '31', '251', '223344'],
            'int. format with leading zero' => ['+31(0251)223344', '31', '251', '223344'],
            'int. format with (0)' => ['+31(0)251-223344', '31', '251', '223344'],
            'int. format with (0) + dashes' => ['+31(0) 251-223344', '31', '251', '223344'],
            'int. format with (0) + spaces + dashes' => ['+31 (0) 251-223344', '31', '251', '223344'],
            'int. format with (0) + spaces' => ['+31 (0) 251 22 33 44', '31', '251', '223344'],
            'int. format with (5)' => ['+31 (5) 251 22 33 44', '31', '525', '1223344'],
            'int. format with superfluous ( )' => ['+31 (0) 251 22 (33) 44', '31', '251', '223344'],
            'dutch with -' => ['020-1234567', '31', '20', '1234567'],
            'dutch with space' => ['020 1234567', '31', '20', '1234567'],
            'dutch with spaces' => ['020 12 345 67', '31', '20', '1234567'],
            'dutch with more spaces' => ['020 1 234 567', '31', '20', '1234567'],
            'dutch no space' => ['0201234567', '31', '20', '1234567'],
            'dutch mobile with -' => ['06-12345678', '31', '6', '12345678'],
            'int. format dutch mobile' => ['+31 (0) 612345678', '31', '6', '12345678'],
            'dutch mobile' => ['0612345678', '31', '6', '12345678'],
            'invalid number' => ['test', '', '', ''],
            'prefixed number' => ['tel.:020-1234567', '31', '20', '1234567'],
            'empty' => ['', '', '', ''],
            'dutch mobile with country code' => ['+31(0)6123456789', '31', '6', '123456789'],
        ];
    }

    /**
     * @dataProvider dataProvider
     */
    public function testExplodePhoneNumber(
        string $value,
        string $countryCode,
        string $areaCode,
        string $subscriberNumber
    ): void {
        $phoneNumber = PhoneNumber::fromPhoneNumberData();
        $phoneNumber->setPhoneNumber($value);
        $expected = empty($countryCode . $areaCode . $subscriberNumber)
            ? null
            : [
                'type' => 'private',
                'countryCode' => $countryCode,
                'areaCode' => $areaCode,
                'subscriberNumber' => $subscriberNumber,
            ];
        self::assertSame($expected, $phoneNumber->getState());
    }

    /**
     * @return array<string, array<string|null>>
     */
    public function provider(): array
    {
        return [
            'c (a) s without countrycode' => ['', '6', '123456789', 'c (a) s', '(06) 123456789'],
            'c (a) s with countrycode' => ['+31', '6', '123456789', 'c (a) s', '+31 (6) 123456789'],
            '(a) s with countrycode' => ['+31', '6', '123456789', '(a) s', '(06) 123456789'],
            'cas with countrycode' => ['+31', '6', '123456789', 'cas', '+316123456789'],
            'as with countrycode' => ['+31', '6', '123456789', 'as', '06123456789'],
            'a-s with countrycode' => ['+31', '6', '123456789', 'a-s', '06-123456789'],
            'no format as international number' => ['+31', '6', '123456789', null, '+31 (6) 123456789'],
            'no format but with leading 0' => ['', '0', '06123456789', null, '06123456789'],
            'unknown format' => ['', '0', '06123456789', 'henk', 'henk'],
            'empty area and subscriber' => ['+31', '', '', '', ''],
            'fix countryCode prefix' => ['31', '251', '123456', null, '+31 (251) 123456'],
            'ignore leading zero' => ['31', '0251', '123456', '(a) s', '(0251) 123456'],
        ];
    }

    /**
     * @dataProvider provider
     */
    public function test_to_string_is_correctly_formatted(
        string $countryCode,
        string $areaCode,
        string $subscriberNumber,
        ?string $format,
        string $expected
    ): void {
        $phoneNumber = PhoneNumber::fromPhoneNumberData(
            [
                'countryCode' => $countryCode,
                'areaCode' => $areaCode,
                'subscriberNumber' => $subscriberNumber,
            ]
        );
        self::assertSame($expected, $phoneNumber->toString($format));
    }

    public function test_it_can_be_cast_to_string(): void
    {
        $phoneNumber = PhoneNumber::fromPhoneNumberData(
            [
                'countryCode' => '+31',
                'areaCode' => '06',
                'subscriberNumber' => '123456789',
            ]
        );
        self::assertSame('+31 (6) 123456789', (string)$phoneNumber);
    }

    public function test_it_can_be_created_from_a_string(): void
    {
        $phoneNumber = PhoneNumber::fromString('+316123456789');
        self::assertSame('+31 (6) 123456789', (string)$phoneNumber);
    }
}
