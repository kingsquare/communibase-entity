<?php

declare(strict_types=1);

namespace Communibase\Tests;

use Communibase\CommunibaseId;
use PHPUnit\Framework\TestCase;

/**
 * @author Kingsquare (source@kingsquare.nl)
 * @copyright Copyright (c) Kingsquare BV (http://www.kingsquare.nl)
 */
class CommunibaseIdTest extends TestCase
{
    private const VALID_ID_STRING = '5c3e042ea3eeb2010324a8e8';
    private const VALID_ID_STRING_2 = '5c3e042951f0be010443a1d2';

    public function test_it_can_be_created(): void
    {
        $id = CommunibaseId::create();
        $this->assertInstanceOf(CommunibaseId::class, $id);
    }

    public function test_it_can_be_created_with_an_empty_string(): void
    {
        $id = CommunibaseId::fromString('');
        $this->assertTrue($id->isEmpty());
    }

    public function test_it_can_be_created_from_a_valid_string(): void
    {
        $id = CommunibaseId::fromString(self::VALID_ID_STRING);
        $this->assertEquals(self::VALID_ID_STRING, $id->toString());
    }

    public function invalidStringSources(): array
    {
        return [
            'string - invalid format' => ['foo'],
            'string - invalid char' => ['5c3e042951f0be010443a1dg']
        ];
    }

    /**
     * @dataProvider invalidStringSources
     * @expectedException \Communibase\Exception\InvalidIdException
     *
     * @param mixed $string
     */
    public function test_it_cant_be_created_from_an_invalid_string($string): void
    {
        CommunibaseId::fromString($string);
    }

    public function test_it_can_be_converted_to_an_objectQueryArray(): void
    {
        $expected = [
            [
                '$ObjectId' => self::VALID_ID_STRING
            ],
            [
                '$ObjectId' => self::VALID_ID_STRING_2
            ]
        ];
        $communibaseIds = CommunibaseId::fromStrings([self::VALID_ID_STRING, self::VALID_ID_STRING_2]);
        $this->assertEquals($expected, CommunibaseId::toObjectQueryArray($communibaseIds));
    }

    public function test_we_can_retrieve_the_createdate(): void
    {
        $id = CommunibaseId::create();
        $this->assertNull($id->getCreateDate());

        $id = CommunibaseId::fromString(self::VALID_ID_STRING);
        $this->assertEquals(
            new \DateTimeImmutable('2019-01-15T16:02:54.000000+0000'),
            $id->getCreateDate()
        );
    }

    public function test_it_can_be_compared_using_equals(): void
    {
        $id = CommunibaseId::fromString(self::VALID_ID_STRING);
        $id2 = CommunibaseId::fromString(self::VALID_ID_STRING);
        $this->assertTrue($id->equals($id2));

        $id2 = CommunibaseId::fromString(self::VALID_ID_STRING_2);
        $this->assertFalse($id->equals($id2));
    }

    public function test_it_can_look_itself_up_in_an_array(): void
    {
        $id = CommunibaseId::fromString(self::VALID_ID_STRING);
        $this->assertTrue(
            $id->inArray([
                CommunibaseId::fromString(self::VALID_ID_STRING),
                CommunibaseId::fromString(self::VALID_ID_STRING)
            ])
        );
        $this->assertFalse($id->inArray([CommunibaseId::fromString(self::VALID_ID_STRING_2)]));
    }

    public function invalidCommunibaseIdArrayProvider(): array
    {
        return [
            [[CommunibaseId::fromString(self::VALID_ID_STRING), 'foo']],
        ];
    }

    /**
     * @dataProvider invalidCommunibaseIdArrayProvider
     * @expectedException \Communibase\Exception\InvalidIdException
     *
     * @param array $ids
     */
    public function test_communibaseId_array_contains_only_communibaseId_objects(array $ids): void
    {
        CommunibaseId::toStrings($ids);
    }

    public function test_it_can_convert_an_array_to_strings(): void
    {
        $this->assertSame(
            [self::VALID_ID_STRING, self::VALID_ID_STRING_2],
            CommunibaseId::toStrings([
                CommunibaseId::fromString(self::VALID_ID_STRING),
                CommunibaseId::fromString(self::VALID_ID_STRING_2),
            ])
        );
    }

    public function test_it_can_be_json_encoded(): void
    {
        $this->assertSame(
            '"' . self::VALID_ID_STRING . '"',
            \json_encode(CommunibaseId::fromString(self::VALID_ID_STRING))
        );
    }
}
