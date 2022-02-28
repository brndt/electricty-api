<?php

declare(strict_types=1);

namespace Electricity\Readings\Domain;

use Closure;

final class ReadingByPeriod
{
    public function __construct(public readonly string $period, public readonly string $reading)
    {
    }

    public static function comparator(): Closure
    {
        return fn(ReadingByPeriod $current, ReadingByPeriod $next): int => $current->reading <=> $next->reading;
    }
}