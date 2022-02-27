<?php

declare(strict_types=1);

namespace Electricity\Readings\Domain;

final class ReadingByPeriod
{
    public function __construct(public readonly string $period, public readonly string $reading)
    {
    }
}