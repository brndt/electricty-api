<?php

declare(strict_types=1);

namespace Electricity\Readings\Application\SearchSuspiciousReadings;

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
        $clientsWithReadings = $this->readingsRepository->all();

        $clientsWithOrderedReadings = map(
            fn(ClientWithReadings $readings) => $readings->sortReadingsByAsc(),
            $clientsWithReadings
        );

        $clientsWithCalculatedMedians = map(
            fn(ClientWithReadings $readings) => ClientWithCalculatedMedian::create(
                $readings->clientId,
                $readings->readings
            ),
            $clientsWithOrderedReadings
        );

        $clientsWithSuspiciousReadings = map(
            fn(ClientWithCalculatedMedian $readings) => $readings->filteredBySuspicious(),
            $clientsWithCalculatedMedians
        );

        $readingsAsResponse = flat_map(
            fn(ClientWithCalculatedMedian $clientWithReadings) => map(
                fn(ReadingByPeriod $readingByPeriod) => new SuspiciousReadingResponse(
                    $clientWithReadings->clientId->value,
                    $readingByPeriod->period,
                    $readingByPeriod->reading,
                    $clientWithReadings->median->asString()
                ),
                $clientWithReadings->readings
            ),
            $clientsWithSuspiciousReadings
        );

        return new SuspiciousReadingCollectionResponse($readingsAsResponse);
    }
}