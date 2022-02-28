<?php

declare(strict_types=1);

namespace Electricity\Readings\Domain;

use function Lambdish\Phunctional\filter;

final class ClientWithCalculatedMedian
{
    private function __construct(
        public readonly ClientId $clientId,
        public readonly ReadingsByPeriodCollection $readings,
        public readonly Median $median,
    ) {
    }


    public static function create(ClientId $clientId, ReadingsByPeriodCollection $readings): ClientWithCalculatedMedian
    {
        $median = new Median(
            ((int)$readings->getIterator()->offsetGet(5)->reading + (int)$readings->getIterator()->offsetGet(
                    6
                )->reading) / 2
        );
        return new self($clientId, $readings, $median);
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