<?php

declare(strict_types=1);

namespace Electricity\Readings\Domain;

use Electricity\Common\Domain\Collection;

use function Lambdish\Phunctional\sort;

final class ReadingsByPeriodCollection extends Collection
{
    public static function type(): string
    {
        return ReadingByPeriod::class;
    }

    public function sortedByAsc(): self
    {
        return new self(sort(ReadingByPeriod::comparator(), $this->items()));
    }

}