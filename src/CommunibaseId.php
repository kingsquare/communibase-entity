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
        if ($id !== '' && !preg_match('/^[a-f0-9]{24}$/', $id)) {
            throw new InvalidIdException('Invalid ID (' . $id . ')');
        }
    }
}
