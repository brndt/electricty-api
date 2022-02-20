<?php

declare(strict_types=1);

namespace Electricity\Readings\Application\SearchSuspiciousReadings;

use Electricity\Readings\Domain\SuspiciousReading;

final class SuspiciousReadingResponse
{
    public function __construct(
        public readonly string $clientId,
        public readonly string $month,
        public readonly string $reading,
        public readonly string $median,
    ) {
    }

    public static function fromSuspiciousReading(SuspiciousReading $suspiciousReading): self
    {
        return new self(
            $suspiciousReading->clientId,
            $suspiciousReading->month,
            $suspiciousReading->reading,
            $suspiciousReading->median,
        );
    }
}