<?php

declare(strict_types=1);

namespace Electricity\Readings\Application\SearchSuspiciousReadings;

use Electricity\Readings\Domain\Reading;
use Electricity\Readings\Domain\Readings;
use Electricity\Readings\Domain\ReadingsRepository;

use Electricity\Readings\Domain\SuspiciousReading;

use function Lambdish\Phunctional\map;

final class SearchSuspiciousReadingsApplicationService
{
    public function __construct(private readonly ReadingsRepository $readingsRepository)
    {
    }

    public function __invoke(): SuspiciousReadingCollectionResponse
    {
        $readings = $this->readingsRepository->all();

        $suspiciousReadings = map(
            fn(Reading $reading): SuspiciousReading => new SuspiciousReading(
                $reading->clientId,
                $reading->period,
                $reading->reading,
                '50',
            ),
            $readings
        );

        $readingsAsResponse = map(
            fn(SuspiciousReading $suspiciousReading) => SuspiciousReadingResponse::fromSuspiciousReading(
                $suspiciousReading
            ), $suspiciousReadings
        );

        return new SuspiciousReadingCollectionResponse($readingsAsResponse);
    }
}