<?php

declare(strict_types=1);

namespace Electricity\Readings\Domain;

final class SuspiciousReading
{
    public function __construct(
        public readonly string $clientId,
        public readonly string $month,
        public readonly string $reading,
        public readonly string $median,
    ) {
    }
}