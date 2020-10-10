<?php

declare(strict_types=1);

namespace Communibase\Tests;

use Communibase\CommunibaseId;
use Communibase\CommunibaseIdCollection;
use Communibase\Exception\InvalidIdException;
use PHPUnit\Framework\TestCase;

class CommunibaseIdCollectionTest extends TestCase
{
    private const VALID_ID_STRING = '5c3e042ea3eeb2010324a8e8';
    private const VALID_ID_STRING_2 = '5c3e042951f0be010443a1d2';

    public function test_it_throws_an_exception_on_invalid_id_string(): void
    {
        $this->expectException(InvalidIdException::class);
        CommunibaseIdCollection::fromStrings([self::VALID_ID_STRING, 'foo']);
    }

    public function test_it_can_count_itself(): void
    {
        $ids = CommunibaseIdCollection::fromStrings([self::VALID_ID_STRING, self::VALID_ID_STRING_2]);
        self::assertEquals(2, $ids->count());
    }

    public function test_ids_are_unique(): void
    {
        $ids = CommunibaseIdCollection::fromStrings([self::VALID_ID_STRING, self::VALID_ID_STRING]);
        self::assertEquals(1, $ids->count());
    }

    public function test_if_it_contains_an_specific_id(): void
    {
        $ids = CommunibaseIdCollection::fromStrings([self::VALID_ID_STRING]);
        self::assertTrue($ids->contains(CommunibaseId::fromString(self::VALID_ID_STRING)));
        self::assertFalse($ids->contains(CommunibaseId::fromString(self::VALID_ID_STRING_2)));
    }

    public function test_for_empty(): void
    {
        $ids = CommunibaseIdCollection::fromStrings([self::VALID_ID_STRING]);
        self::assertFalse($ids->isEmpty());
        self::assertTrue(CommunibaseIdCollection::fromStrings([])->isEmpty());
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
        $ids = CommunibaseIdCollection::fromStrings([self::VALID_ID_STRING, self::VALID_ID_STRING_2]);
        self::assertEquals($expected, $ids->toObjectQueryArray());
    }

    public function test_to_strings(): void
    {
        $strings = [self::VALID_ID_STRING, self::VALID_ID_STRING_2];
        $ids = CommunibaseIdCollection::fromStrings($strings);
        self::assertEquals($strings, $ids->toStrings());
    }

    public function test_it_can_be_json_serialized(): void
    {
        $ids = CommunibaseIdCollection::fromStrings([self::VALID_ID_STRING]);
        self::assertEquals('["' . self::VALID_ID_STRING . '"]', \json_encode($ids));
    }

    public function test_it_can_be_traversed(): void
    {
        $ids = CommunibaseIdCollection::fromStrings([self::VALID_ID_STRING, self::VALID_ID_STRING_2]);
        $results = [];
        foreach ($ids as $id) {
            $results[] = $id->toString();
        }
        self::assertSame([self::VALID_ID_STRING, self::VALID_ID_STRING_2], $results);
    }
}
