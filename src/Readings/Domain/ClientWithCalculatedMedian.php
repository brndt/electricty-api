<?php

declare(strict_types=1);

namespace Electricity\Readings\Domain;

use function Lambdish\Phunctional\filter;
use function Lambdish\Phunctional\sort;

final class ClientWithCalculatedMedian
{
    private function __construct(
        public readonly ClientId $clientId,
        public readonly array $readings,
        public readonly Median $median,
    ) {
    }


    public static function create(ClientId $clientId, array $readings): ClientWithCalculatedMedian
    {
        $median = new Median(
            ((int)$readings[5]->reading + (int)$readings[6]->reading) / 2
        );
        return new self($clientId, $readings, $median);
    }

    public function filteredBySuspicious(): ClientWithCalculatedMedian
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
}