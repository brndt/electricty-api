<?php

declare(strict_types=1);

namespace Electricity\Readings\Domain;

final class Median
{
    public const NON_SUSPICIOUS_PERCENTAGE = 50;
    public readonly float $minNonSuspiciousValue;
    public readonly float $maxNonSuspiciousValue;

    public function __construct(public readonly float $value)
    {
        $this->minNonSuspiciousValue = $value - $value * self::NON_SUSPICIOUS_PERCENTAGE * 0.01;
        $this->maxNonSuspiciousValue = $value + $value * self::NON_SUSPICIOUS_PERCENTAGE * 0.01;
    }

    public function asString(): string
    {
        return (string)$this->value;
    }
}