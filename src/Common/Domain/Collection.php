<?php

declare(strict_types=1);

namespace Electricity\Common\Domain;

use ArrayIterator;
use Countable;

use IteratorAggregate;

use function Lambdish\Phunctional\each;
use function Lambdish\Phunctional\flat_map;

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
        return \count($this->items());
    }

    abstract public static function fromPrimitives(array $primitives): static;

    abstract public function toPrimitives(): array;

    final public function value(): array
    {
        return $this->toPrimitives();
    }

    public static function mergeWithUniqueness(self ...$arrayCollections): static
    {
        $mergedCollection = flat_map(fn(self $collection) => $collection->items(), $arrayCollections);

        return new static(array_values(array_unique($mergedCollection)));
    }

    public function contains(mixed $item): bool
    {
        Assert::instanceOf(static::type(), $item);

        return \in_array($item, $this->items(), false);
    }

    public function copyWith(mixed $item): static
    {
        Assert::instanceOf(static::type(), $item);

        return new static([$item, ...$this]);
    }

    public function copyWithUniqueness(mixed $item): static
    {
        Assert::instanceOf(static::type(), $item);

        return new static(array_values(array_unique([$item, ...$this])));
    }

    protected function each(callable $fn): void
    {
        each($fn, $this->items());
    }
}