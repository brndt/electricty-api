<?php

declare(strict_types=1);

namespace Electricity\Common\Domain\Criteria;

use Electricity\Common\Domain\Collection;

use function Lambdish\Phunctional\map;
use function Lambdish\Phunctional\reduce;

final class Filters extends Collection
{
    public static function fromPrimitives(array $primitives): static
    {
        return new self(array_map(self::filterBuilder(), $primitives));
    }

    public function filters(): array
    {
        return $this->items();
    }

    public function serialize(): string
    {
        return reduce(
            static fn (string $accumulate, Filter $filter) => sprintf('%s^%s', $accumulate, $filter->serialize()),
            $this->items(),
            ''
        );
    }

    public function toPrimitives(): array
    {
        return map(
            fn (Filter $filter) => [
                'field' => $filter->field()->value,
                'operator' => $filter->operator()->value,
                'value' => $filter->value()->value,
            ],
            $this
        );
    }

    public static function type(): string
    {
        return Filter::class;
    }

    private static function filterBuilder(): callable
    {
        return fn (array $values) => Filter::fromValues($values);
    }
}
