<?php

declare(strict_types=1);

namespace Communibase\Tests;

use Communibase\CommunibaseId;
use Communibase\DocumentReference;
use Communibase\Entity\Address;
use Communibase\Entity\Email;
use Communibase\Entity\PhoneNumber;
use Communibase\Exception\InvalidEntityException;
use PHPUnit\Framework\TestCase;

/**
 * @author Kingsquare (source@kingsquare.nl)
 * @copyright Copyright (c) Kingsquare BV (http://www.kingsquare.nl)
 */
class DocumentReferenceTest extends TestCase
{
    private const VALID_ID_STRING = '5c3e042ea3eeb2010324a8e8';
    private const VALID_ID_STRING_2 = '5c3e042951f0be010443a1d2';

    public function test_it_can_be_created(): void
    {
        DocumentReference::create('Person', CommunibaseId::create());
    }

    public function test_it_will_throw_exception_when_wrong_casing_is_passed(): void
    {
        $this->expectException(InvalidEntityException::class);
        DocumentReference::create('person', CommunibaseId::create());
    }

    public function test_it_can_create_reference_for_email(): void
    {
        $email = Email::fromEmailAddressData(['_id' => self::VALID_ID_STRING, 'emailAddress' => 'test@kingsquare.nl']);
        $result = DocumentReference::create('Person', CommunibaseId::fromValidString(self::VALID_ID_STRING_2))->toEmail(
            $email
        );

        self::assertSame(
            [
                'documentReference' => [
                    'rootDocumentId' => self::VALID_ID_STRING_2,
                    'rootDocumentEntityType' => 'Person',
                ],
                'path' => [
                    [
                        'field' => 'emailAddresses',
                        'objectId' => self::VALID_ID_STRING,
                    ],
                ]
            ],
            $result
        );
    }

    public function test_it_can_create_reference_for_address(): void
    {
        $address = Address::fromAddressData(
            ['_id' => self::VALID_ID_STRING, 'street' => 'street', 'streetNumber' => 12, 'zipcode' => '1234aa']
        );
        $result = DocumentReference::create(
            'Person',
            CommunibaseId::fromValidString(self::VALID_ID_STRING_2)
        )->toAddress(
            $address
        );

        self::assertSame(
            [
                'documentReference' => [
                    'rootDocumentId' => self::VALID_ID_STRING_2,
                    'rootDocumentEntityType' => 'Person',
                ],
                'path' => [
                    [
                        'field' => 'addresses',
                        'objectId' => self::VALID_ID_STRING,
                    ],
                ]
            ],
            $result
        );
    }

    public function test_it_can_create_reference_for_phonenumber(): void
    {
        $phoneNumber = PhoneNumber::fromPhoneNumberData(
            ['_id' => self::VALID_ID_STRING, 'areaCode' => 12, 'subscriberNumber' => '1234']
        );
        $result = DocumentReference::create(
            'Person',
            CommunibaseId::fromValidString(self::VALID_ID_STRING_2)
        )->toPhoneNumber(
            $phoneNumber
        );

        self::assertSame(
            [
                'documentReference' => [
                    'rootDocumentId' => self::VALID_ID_STRING_2,
                    'rootDocumentEntityType' => 'Person',
                ],
                'path' => [
                    [
                        'field' => 'phoneNumbers',
                        'objectId' => self::VALID_ID_STRING,
                    ],
                ]
            ],
            $result
        );
    }

    public function test_it_can_create_reference_for_a_full_entity(): void
    {
        $result = DocumentReference::create(
            'Person',
            CommunibaseId::fromValidString(self::VALID_ID_STRING_2)
        )->toEntity();

        self::assertSame(
            [
                'documentReference' => [
                    'rootDocumentId' => self::VALID_ID_STRING_2,
                    'rootDocumentEntityType' => 'Person',
                ]
            ],
            $result
        );
    }

    public function test_it_will_return_empty_if_source_entity_id_is_empty():void
    {
        self::assertSame([], DocumentReference::create('Person', CommunibaseId::fromValidString())->toEntity());
    }
    public function test_it_will_return_empty_if_source_entity_is_empty():void
    {
        self::assertSame([], DocumentReference::create('', CommunibaseId::fromValidString(self::VALID_ID_STRING))->toEntity());
    }
}
