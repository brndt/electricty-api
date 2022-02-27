<?php

declare(strict_types=1);

namespace Electricity\Readings\Domain;

use function Lambdish\Phunctional\filter;
use function Lambdish\Phunctional\sort;

final class ClientWithReadings
{
    public function __construct(
        public readonly ClientId $clientId,
        public readonly array $readings,
        private ?Median $median = null,
    ) {
    }

    public function sortReadingsByAsc(): self
    {
        return
            new self(
                $this->clientId, sort(
                fn(
                    ReadingByPeriod $oneReading,
                    ReadingByPeriod $otherReading
                ): int => $oneReading->reading <=> $otherReading->reading,
                $this->readings
            )
            );
    }

    public function withCalculatedMedian(): ClientWithReadings
    {
        $median = new Median(
            ((int)$this->readings[5]->reading + (int)$this->readings[6]->reading) / 2
        );
        return new self($this->clientId, $this->readings, $median);
    }

    public function filteredBySuspiciousReadings(): ClientWithReadings
    {
        $suspiciousReadings = filter(
            fn(ReadingByPeriod $reading) => $this->isSuspicious($reading),
            $this->readings
        );
        return new self($this->clientId, $suspiciousReadings, $this->median);
    }

    private function isSuspicious(ReadingByPeriod $reading): bool
    {
        return $this->median->maxNonSuspiciousValue <= (float)$reading->reading || $this->median->minNonSuspiciousValue >= (float)$reading->reading;
    }

    public function median(): ?Median
    {
        return $this->median;
    }
}