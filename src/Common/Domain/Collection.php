<?php

declare(strict_types=1);

namespace Electricity\Common\Domain;

use ArrayIterator;
use Countable;
use IteratorAggregate;

use function count;
use function in_array;
use function Lambdish\Phunctional\each;

abstract class Collection implements Countable, IteratorAggregate
{
    private array $items;

    final public function __construct(array $items)
    {
        Assert::arrayOf(static::type(), $items);

        $this->items = $items;
    }

    abstract public static function type(): string;

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->items());
    }

    public function items(): array
    {
        return $this->items;
    }

    public function count(): int
    {
        return count($this->items());
    }

    public function contains(mixed $item): bool
    {
        Assert::instanceOf(static::type(), $item);

        return in_array($item, $this->items(), false);
    }

    protected function each(callable $fn): void
    {
        each($fn, $this->items());
    }
}