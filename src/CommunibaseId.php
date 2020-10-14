<?php

declare(strict_types=1);

namespace Communibase;

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
     * Assume the string is valid so we don't need to catch a possible exception.
     * CommunibaseId->isEmpty() === true if the string is invalid.
     */
    public static function fromValidString(string $string = null): CommunibaseId
    {
        try {
            return self::fromString($string);
        } catch (InvalidIdException $e) {
            return self::create();
        }
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

    /** @noinspection PhpUnhandledExceptionInspection */
    public function getCreateDate(): ?\DateTimeImmutable
    {
        if ($this->isEmpty()) {
            return null;
        }
        $timestamp = intval(substr($this->id, 0, 8), 16);
        return new \DateTimeImmutable('@' . $timestamp);
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
        if ($id !== '' && !preg_match('/^[a-f0-9]{24}$/', $id)) {
            throw new InvalidIdException('Invalid ID (' . $id . ')');
        }
    }
}
