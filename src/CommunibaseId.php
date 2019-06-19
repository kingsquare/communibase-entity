<?php

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
     * @var string|null
     */
    private $id;

    /**
     * @param string|null $id
     */
    private function __construct($id = null)
    {
        $this->id = $id;
    }

    /**
     * @return CommunibaseId
     */
    public static function create()
    {
        return new self();
    }

    /**
     * @param $string |null
     *
     * @return CommunibaseId
     * @throws InvalidIdException
     */
    public static function fromString($string = null)
    {
        self::guardAgainstInvalidIdString($string);
        return new self($string);
    }

    /**
     * @param array $strings
     *
     * @return CommunibaseId[]
     * @throws InvalidIdException
     */
    public static function fromStrings(array $strings)
    {
        return array_map(static function ($string) {
            return self::fromString($string);
        }, $strings);
    }

    /**
     * @param CommunibaseId[] $ids
     *
     * @return array
     */
    public static function toObjectQueryArray(array $ids)
    {
        return array_reduce($ids, static function (array $carry, CommunibaseId $id) {
            $carry[] = ['$ObjectId' => $id->toString()];
            return $carry;
        }, []);
    }

    /**
     * @param CommunibaseId[] $ids
     *
     * @return array|string[]
     */
    public static function toStrings(array $ids)
    {
        return \array_values(array_filter(array_map('strval', $ids)));
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->id;
    }

    /**
     * @return string
     */
    public function toString()
    {
        return $this->__toString();
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return empty($this->id);
    }

    /**
     * @param CommunibaseId $id
     *
     * @return bool
     */
    public function equals(CommunibaseId $id)
    {
        return $this->toString() === $id->toString();
    }

    /**
     * @param CommunibaseId[] $ids
     *
     * @return bool
     */
    public function inArray(array $ids)
    {
        return in_array($this->id, self::toStrings($ids), true);
    }

    /**
     * @return \DateTimeImmutable|null
     * @throws InvalidDateTimeException
     */
    public function getCreateDate()
    {
        if ($this->isEmpty()) {
            return null;
        }
        try {
            $timestamp = intval(substr($this, 0, 8), 16);
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
        return (string)$this->id;
    }

    /**
     * @param string|null $id
     *
     * @throws InvalidIdException
     */
    private static function guardAgainstInvalidIdString($id)
    {
        if (!empty($id) && !preg_match('/^[a-f0-9]{24}$/', $id)) {
            throw new InvalidIdException('Invalid ID (' . $id . ')');
        }
    }
}
