<?php

declare(strict_types=1);

namespace Electricity\Readings\Infrastructure\Persistence\Csv;

use Closure;
use Electricity\Readings\Domain\ClientId;
use Electricity\Readings\Domain\ClientWithReadings;
use Electricity\Readings\Domain\ReadingByPeriod;
use Electricity\Readings\Domain\ReadingsByPeriodCollection;
use Electricity\Readings\Domain\ReadingsRepository;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Encoder\CsvEncoder;

use function Lambdish\Phunctional\group_by;
use function Lambdish\Phunctional\map;

final class CsvReadingsRepository implements ReadingsRepository
{
    private array $readings;

    public function __construct(File $file, CsvEncoder $encoder)
    {
        $this->readings = $encoder->decode($file->getContent(), 'xml');
    }

    public function all(): array
    {
        $readingsGroupedByClientId = group_by(fn($reading): string => $reading['client'], $this->readings);

        return $this->buildClientsWithReadings($readingsGroupedByClientId);
    }

    private function buildClientsWithReadings(array $readingsGroupedByClientId): array
    {
        return map($this->clientWithReadingsClosure(), $readingsGroupedByClientId);
    }

    private function clientWithReadingsClosure(): Closure
    {
        return fn(array $clientReadings, string $clientId): ClientWithReadings => new ClientWithReadings(
            new ClientId($clientId),
            new ReadingsByPeriodCollection(map($this->readingsByPeriodClosure(), $clientReadings)),
        );
    }

    private function readingsByPeriodClosure(): Closure
    {
        return fn(array $reading) => new ReadingByPeriod($reading['period'], $reading['reading']);
    }
}
