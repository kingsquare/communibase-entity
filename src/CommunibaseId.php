<?php

declare(strict_types=1);

namespace Communibase;

use Communibase\Exception\InvalidDateTimeException;
use Communibase\Exception\InvalidIdException;

/**
 * Communibase ID
 *
 * @author Kingsquare (source@kingsquare.nl)
 * @copyright Copyright (c) Kingsquare BV (http://www.kingsquare.nl)
 */
final class CommunibaseId implements \JsonSerializable
{
    /**
     * @var string
     */
    private $id;

    private function __construct(string $id = '')
    {
        $this->id = $id;
    }

    public static function create(): CommunibaseId
    {
        return new self();
    }

    /**
     * @throws InvalidIdException
     */
    public static function fromString(string $string = null): CommunibaseId
    {
        if ($string === null) {
            $string = '';
        }
        self::guardAgainstInvalidIdString($string);
        return new self($string);
    }

    /**
     * @return CommunibaseId[]
     * @throws InvalidIdException
     */
    public static function fromStrings(array $strings): array
    {
        return array_map([__CLASS__, 'fromString'], $strings);
    }

    /**
     * @param CommunibaseId[] $ids
     */
    public static function toObjectQueryArray(array $ids): array
    {
        return array_reduce(
            $ids,
            static function (array $carry, CommunibaseId $id) {
                $carry[] = ['$ObjectId' => $id->toString()];
                return $carry;
            },
            []
        );
    }

    /**
     * @param CommunibaseId[] $ids
     *
     * @return array|string[]
     * @throws InvalidIdException
     */
    public static function toStrings(array $ids): array
    {
        self::guardAgainstNonCommunibaseIdObjects($ids);
        return \array_values(array_filter(array_map('strval', $ids)));
    }

    public function __toString(): string
    {
        return $this->id;
    }

    public function toString(): string
    {
        return $this->__toString();
    }

    public function isEmpty(): bool
    {
        return $this->id === '';
    }

    public function equals(CommunibaseId $id): bool
    {
        return $this->toString() === $id->toString();
    }

    /**
     * @param CommunibaseId[] $ids
     *
     * @throws InvalidIdException
     */
    public function inArray(array $ids): bool
    {
        self::guardAgainstNonCommunibaseIdObjects($ids);
        return in_array($this->id, self::toStrings($ids), true);
    }

    /**
     * @throws InvalidDateTimeException
     */
    public function getCreateDate(): ?\DateTimeImmutable
    {
        if ($this->isEmpty()) {
            return null;
        }
        try {
            $timestamp = intval(substr($this->id, 0, 8), 16);
            return new \DateTimeImmutable('@' . $timestamp);
        } catch (\Exception $e) {
            throw new InvalidDateTimeException('Invalid timestamp.', 0, $e);
        }
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        return $this->id;
    }

    /**
     * @throws InvalidIdException
     */
    private static function guardAgainstInvalidIdString(string $id): void
    {
        if (!is_string($id)) {
            throw new InvalidIdException('Invalid ID (type should be string, ' . gettype($id) . ' given)');
        }

        if ($id !== '' && !preg_match('/^[a-f0-9]{24}$/', $id)) {
            throw new InvalidIdException('Invalid ID (' . $id . ')');
        }
    }

    /**
     * @throws InvalidIdException
     */
    private static function guardAgainstNonCommunibaseIdObjects(array $ids): void
    {
        foreach ($ids as $id) {
            if (!$id instanceof self) {
                throw new InvalidIdException('Non CommunibaseId object found in array.');
            }
        }
    }
}
