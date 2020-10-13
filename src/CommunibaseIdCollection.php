<?php

declare(strict_types=1);

namespace Communibase;

use Communibase\Exception\InvalidIdException;

/**
 * @author Kingsquare (source@kingsquare.nl)
 * @copyright Copyright (c) Kingsquare BV (http://www.kingsquare.nl)
 */
class CommunibaseIdCollection implements \Countable, \IteratorAggregate, \JsonSerializable
{
    /**
     * @var CommunibaseId[]
     */
    private $ids;

    /**
     * @throws InvalidIdException
     */
    private function __construct(array $strings)
    {
        $this->ids = \array_map(
            static function (string $string) {
                return CommunibaseId::fromString($string);
            },
            \array_filter(\array_unique($strings))
        );
    }

    /**
     * @throws InvalidIdException
     */
    public static function fromStrings(array $strings): CommunibaseIdCollection
    {
        return new self($strings);
    }

    /**
     * Filter out all invalid strings
     */
    public static function fromValidStrings(array $strings): CommunibaseIdCollection
    {
        $collection = new self([]);
        $collection->ids = \array_reduce(
            $strings,
            static function (array $communibaseIds, $string) {
                try {
                    $communibaseIds[] = CommunibaseId::fromString((string)$string);
                } catch (InvalidIdException $e) {
                    // ignore invalid strings
                }
                return $communibaseIds;
            },
            []
        );
        return $collection;
    }

    public function contains(CommunibaseId $needleId): bool
    {
        foreach ($this->ids as $id) {
            if ($id->equals($needleId)) {
                return true;
            }
        }
        return false;
    }

    public function count(): int
    {
        return count($this->ids);
    }

    public function isEmpty(): bool
    {
        return empty($this->ids);
    }

    /**
     * @return \ArrayIterator|\Traversable|CommunibaseId[]
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->ids);
    }

    /**
     * @return array|string[]
     */
    public function toStrings(): array
    {
        return \array_map('strval', $this->ids);
    }

    public function toObjectQueryArray(): array
    {
        return array_reduce(
            $this->ids,
            static function (array $carry, CommunibaseId $id) {
                $carry[] = ['$ObjectId' => $id->toString()];
                return $carry;
            },
            []
        );
    }

    public function jsonSerialize()
    {
        return $this->toStrings();
    }
}
