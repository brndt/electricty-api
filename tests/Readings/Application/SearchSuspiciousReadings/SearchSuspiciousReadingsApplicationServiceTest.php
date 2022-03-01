<?php

declare(strict_types=1);

namespace Electricity\Tests\Readings\Application\SearchSuspiciousReadings;

use Electricity\Readings\Application\SearchSuspiciousReadings\SearchSuspiciousReadingsApplicationService;
use Electricity\Readings\Application\SearchSuspiciousReadings\SuspiciousReadingCollectionResponse;
use Electricity\Readings\Application\SearchSuspiciousReadings\SuspiciousReadingResponse;
use Electricity\Readings\Domain\ClientId;
use Electricity\Readings\Domain\ClientWithReadings;
use Electricity\Readings\Domain\ReadingByPeriod;
use Electricity\Readings\Domain\ReadingsByPeriodCollection;
use Electricity\Tests\Readings\ReadingsUnitTestCase;

final class SearchSuspiciousReadingsApplicationServiceTest extends ReadingsUnitTestCase
{
    private SearchSuspiciousReadingsApplicationService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new SearchSuspiciousReadingsApplicationService($this->repository());
    }

    public function testItShouldReturnOneSuspiciousReading(): void
    {
        // Given
        $clientsWithReadings = new ClientWithReadings(
            new ClientId('583ef6329d7b9'),
            new ReadingsByPeriodCollection(
                [
                    new ReadingByPeriod('2016-01', '42451'),
                    new ReadingByPeriod('2016-02', '44279'),
                    new ReadingByPeriod('2016-03', '44055'),
                    new ReadingByPeriod('2016-04', '40953'),
                    new ReadingByPeriod('2016-05', '42566'),
                    new ReadingByPeriod('2016-06', '41216'),
                    new ReadingByPeriod('2016-07', '43597'),
                    new ReadingByPeriod('2016-08', '43324'),
                    new ReadingByPeriod('2016-09', '3564'),
                    new ReadingByPeriod('2016-10', '44459'),
                    new ReadingByPeriod('2016-11', '42997'),
                    new ReadingByPeriod('2016-11', '42600'),
                ]
            )
        );

        // When
        $this->shouldReturnAll($clientsWithReadings);
        $expectedResponse = new SuspiciousReadingCollectionResponse(
            [new SuspiciousReadingResponse('583ef6329d7b9', '2016-09', '3564', '42798.5')]
        );

        // Then
        $actualResponse = ($this->service)();
        self::assertEquals($actualResponse, $expectedResponse);
    }

    public function testItShouldReturnEmptyCollectionWhenReadingsDoNotExist(): void
    {
        // Given

        // When
        $this->shouldReturnAll();
        $expectedResponse = new SuspiciousReadingCollectionResponse([]);

        // Then
        $actualResponse = ($this->service)();
        self::assertEquals($actualResponse, $expectedResponse);
    }
}