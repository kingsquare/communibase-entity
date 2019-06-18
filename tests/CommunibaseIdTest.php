<?php

namespace Communibase\Tests;

use Communibase\CommunibaseId;
use PHPUnit\Framework\TestCase;

/**
 * @author Kingsquare (source@kingsquare.nl)
 * @copyright Copyright (c) Kingsquare BV (http://www.kingsquare.nl)
 */
class CommunibaseIdTest extends TestCase
{
    const VALID_ID_STRING = '5c3e042ea3eeb2010324a8e8';
    const VALID_ID_STRING_2 = '5c3e042951f0be010443a1d2';

    public function test_it_can_be_created()
    {
        $id = CommunibaseId::create();
        $this->assertInstanceOf(CommunibaseId::class, $id);
    }

    public function test_it_can_be_created_with_an_empty_string()
    {
        $id = CommunibaseId::fromString('');
        $this->assertTrue($id->isEmpty());
    }

    public function test_it_can_be_created_from_a_valid_string()
    {
        $id = CommunibaseId::fromString(self::VALID_ID_STRING);
        $this->assertEquals(self::VALID_ID_STRING, $id->toString());
    }

    public function invalidStrings()
    {
        return [
            ['foo'],
            ['5c3e042951f0be010443a1dg']
        ];
    }

    /**
     * @dataProvider invalidStrings
     * @expectedException \Communibase\Exception\InvalidIdException
     *
     * @param $string
     */
    public function test_it_cant_be_created_from_an_invalid_string($string)
    {
        CommunibaseId::fromString($string);
    }

    public function test_it_can_be_converted_to_an_objectQueryArray()
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

    public function test_we_can_retrieve_the_createdate()
    {
        $id = CommunibaseId::create();
        $this->assertNull($id->getCreateDate());

        $id = CommunibaseId::fromString(self::VALID_ID_STRING);
        $this->assertEquals(
            new \DateTimeImmutable('2019-01-15T16:02:54.000000+0000'),
            $id->getCreateDate()
        );
    }

    public function test_it_can_be_compared_using_equals()
    {
        $id = CommunibaseId::fromString(self::VALID_ID_STRING);
        $id2 = CommunibaseId::fromString(self::VALID_ID_STRING);
        $this->assertTrue($id->equals($id2));

        $id2 = CommunibaseId::fromString(self::VALID_ID_STRING_2);
        $this->assertFalse($id->equals($id2));
    }

    public function test_it_can_look_itself_up_in_an_array()
    {
        $id = CommunibaseId::fromString(self::VALID_ID_STRING);
        $this->assertTrue($id->inArray([self::VALID_ID_STRING_2, self::VALID_ID_STRING]));
        $this->assertFalse($id->inArray([self::VALID_ID_STRING_2]));
    }

    public function test_it_can_convert_an_array_to_strings()
    {
        $this->assertSame(
            [self::VALID_ID_STRING, self::VALID_ID_STRING_2
                ],
            CommunibaseId::toStrings([
                CommunibaseId::fromString(self::VALID_ID_STRING),
                CommunibaseId::fromString(self::VALID_ID_STRING_2),
            ])
        );
    }

    public function test_it_can_be_json_encoded()
    {
        $this->assertSame(
            '"' . self::VALID_ID_STRING . '"',
            \json_encode(CommunibaseId::fromString(self::VALID_ID_STRING))
        );
    }
}
