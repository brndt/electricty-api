<?php

declare(strict_types=1);

namespace Electricity\Readings\Application\SearchSuspiciousReadings;

final class SuspiciousReadingResponse
{
    public function __construct(
        public readonly string $clientId,
        public readonly string $month,
        public readonly string $reading,
        public readonly string $median,
    ) {
    }
}