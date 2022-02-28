<?php

declare(strict_types=1);

namespace Electricity\Readings\Infrastructure\Persistence\Csv;

use Electricity\Readings\Domain\ClientId;
use Electricity\Readings\Domain\ClientWithReadings;
use Electricity\Readings\Domain\ReadingByPeriod;
use Electricity\Readings\Domain\ReadingsRepository;
use League\Csv\Reader;

use Symfony\Component\HttpFoundation\File\File;

use function Lambdish\Phunctional\group_by;
use function Lambdish\Phunctional\map;

final class CsvReadingsRepository implements ReadingsRepository
{
    private Reader $csvReader;

    public function __construct(File $file)
    {
        $this->csvReader = Reader::createFromFileObject($file->openFile());
        $this->csvReader->setHeaderOffset(0);
    }

    public function all(): array
    {
        $readingsAsPrimitives = $this->csvReader->getRecords();

        $readingsGroupedByClientId = group_by(
            fn($readingAsPrimitive): string => $readingAsPrimitive['client'],
            $readingsAsPrimitives
        );

        return $this->readingsFromPrimitives($readingsGroupedByClientId);
    }

    private function readingsFromPrimitives(array $readingsGroupedByClientId): array
    {
        return map(
            fn(array $clientReadings, string $clientId): ClientWithReadings => new ClientWithReadings(
                new ClientId($clientId), map(fn(array $readingRow) => new ReadingByPeriod(
                $readingRow['period'],
                $readingRow['reading']
            ), $clientReadings),
            ),
            $readingsGroupedByClientId
        );
    }
}
