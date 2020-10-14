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

    /**
     * @doesNotPerformAssertions
     */
    public function test_it_can_create(): void
    {
        CommunibaseId::create();
    }

    public function test_it_can_be_created_with_an_empty_string(): void
    {
        $id = CommunibaseId::fromString('');
        self::assertTrue($id->isEmpty());
    }

    public function test_it_casts_null_to_empty_string(): void
    {
        $id = CommunibaseId::fromString(null);
        self::assertSame('', $id->toString());
    }

    public function test_it_can_be_created_from_a_valid_string(): void
    {
        $id = CommunibaseId::fromString(self::VALID_ID_STRING);
        self::assertEquals(self::VALID_ID_STRING, $id->toString());
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
     */
    public function test_it_cant_be_created_from_an_invalid_string(string $string): void
    {
        $this->expectException(\Communibase\Exception\InvalidIdException::class);
        CommunibaseId::fromString($string);
    }

    public function test_we_can_retrieve_the_createdate(): void
    {
        $id = CommunibaseId::create();
        self::assertNull($id->getCreateDate());

        $id = CommunibaseId::fromString(self::VALID_ID_STRING);
        self::assertEquals(
            new \DateTimeImmutable('2019-01-15T16:02:54.000000+0000'),
            $id->getCreateDate()
        );
    }

    public function test_it_can_be_compared_using_equals(): void
    {
        $id = CommunibaseId::fromString(self::VALID_ID_STRING);
        $id2 = CommunibaseId::fromString(self::VALID_ID_STRING);
        self::assertTrue($id->equals($id2));

        $id2 = CommunibaseId::fromString(self::VALID_ID_STRING_2);
        self::assertFalse($id->equals($id2));
    }

    public function test_it_can_be_json_encoded(): void
    {
        self::assertSame(
            '"' . self::VALID_ID_STRING . '"',
            \json_encode(CommunibaseId::fromString(self::VALID_ID_STRING))
        );
    }

    public function test_invalid_string_returns_an_empty_communibaseId(): void
    {
        $id = CommunibaseId::fromValidString('foo');
        self::assertTrue($id->isEmpty());
    }
}
