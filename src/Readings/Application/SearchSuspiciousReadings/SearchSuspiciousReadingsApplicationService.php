<?php

declare(strict_types=1);

namespace Electricity\Readings\Application\SearchSuspiciousReadings;

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
            fn(ClientWithReadings $clientReadings) => $clientReadings->sortReadingsByAsc(),
            $clientsWithReadings
        );

        $clientsWithCalculatedMedians = map(
            fn(ClientWithReadings $clientReadings) => $clientReadings->withCalculatedMedian(),
            $clientsWithOrderedReadings
        );


        $clientsWithSuspiciousReadings =
            map(
                fn(ClientWithReadings $clientReadings) => $clientReadings->filteredBySuspiciousReadings(),
                $clientsWithCalculatedMedians
            );

        $readingsAsResponse = flat_map(
            fn(ClientWithReadings $clientWithReadings) => map(
                fn(ReadingByPeriod $readingByPeriod) => new SuspiciousReadingResponse(
                    $clientWithReadings->clientId->value,
                    $readingByPeriod->period,
                    $readingByPeriod->reading,
                    $clientWithReadings->median()->asString()
                ),
                $clientWithReadings->readings
            ),
            $clientsWithSuspiciousReadings
        );

        return new SuspiciousReadingCollectionResponse($readingsAsResponse);
    }
}