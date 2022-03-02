<?php

declare(strict_types=1);

namespace Electricity\Readings\Domain;

use function Lambdish\Phunctional\filter;

final class ClientWithCalculatedMedian
{
    public function __construct(
        public readonly ClientId $clientId,
        public readonly ReadingsByPeriodCollection $readings,
        public readonly Median $median,
    ) {
    }

    public static function fromClientWithReadings(ClientWithReadings $clientWithReadings): self
    {
        $median = Median::fromReadings($clientWithReadings->readings);
        return new self(
            $clientWithReadings->clientId, $clientWithReadings->readings, $median
        );
    }

    public function filteredBySuspicious(): ClientWithCalculatedMedian
    {
        $suspiciousReadings = new ReadingsByPeriodCollection(
            filter(
                fn(ReadingByPeriod $reading) => $this->isSuspicious($reading),
                $this->readings->items()
            )
        );
        return new self($this->clientId, $suspiciousReadings, $this->median);
    }

    private function isSuspicious(ReadingByPeriod $reading): bool
    {
        return $this->median->maxNonSuspiciousValue <= (float)$reading->reading || $this->median->minNonSuspiciousValue >= (float)$reading->reading;
    }
}