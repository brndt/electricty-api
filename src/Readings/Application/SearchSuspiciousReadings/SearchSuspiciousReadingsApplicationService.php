<?php

declare(strict_types=1);

namespace Electricity\Readings\Application\SearchSuspiciousReadings;

use Closure;
use Electricity\Readings\Domain\ClientWithCalculatedMedian;
use Electricity\Readings\Domain\ClientWithReadings;
use Electricity\Readings\Domain\ReadingByPeriod;
use Electricity\Readings\Domain\ReadingsRepository;

use function Lambdish\Phunctional\flat_map;
use function Lambdish\Phunctional\map;

final class SearchSuspiciousReadingsApplicationService
{
    public function __construct(private readonly ReadingsRepository $readingsRepository)
    {
    }

    public function __invoke(): SuspiciousReadingCollectionResponse
    {
        $readings = $this->readingsRepository->all();

        $sortedReadings = map($this->sortReadingsForEveryClient(), $readings);

        $readingsWithCalculatedMedian = map($this->calculateMedianForClient(), $sortedReadings);

        $readingsFilteredBySuspicious = map($this->searchSuspiciousReadingsForClient(), $readingsWithCalculatedMedian);

        $readingsAsResponse = flat_map($this->suspiciousReadingsResponseExtractor(), $readingsFilteredBySuspicious);

        return new SuspiciousReadingCollectionResponse($readingsAsResponse);
    }

    private function sortReadingsForEveryClient(): Closure
    {
        return fn(ClientWithReadings $readings) => $readings->sortReadingsByAsc();
    }

    private function calculateMedianForClient(): Closure
    {
        return fn(ClientWithReadings $readings) => ClientWithCalculatedMedian::create(
            $readings->clientId,
            $readings->readings
        );
    }

    private function searchSuspiciousReadingsForClient(): Closure
    {
        return fn(ClientWithCalculatedMedian $readings) => $readings->filteredBySuspicious();
    }

    private function suspiciousReadingsResponseExtractor(): Closure
    {
        return fn(ClientWithCalculatedMedian $clientWithReadings) => map(
            $this->suspiciousReadingResponseExtractor($clientWithReadings),
            $clientWithReadings->readings
        );
    }

    private function suspiciousReadingResponseExtractor(ClientWithCalculatedMedian $clientWithReadings): Closure
    {
        return fn(ReadingByPeriod $readingByPeriod) => new SuspiciousReadingResponse(
            $clientWithReadings->clientId->value,
            $readingByPeriod->period,
            $readingByPeriod->reading,
            $clientWithReadings->median->asString()
        );
    }
}