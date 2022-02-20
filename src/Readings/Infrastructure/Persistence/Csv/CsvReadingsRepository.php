<?php

declare(strict_types=1);

namespace Electricity\Readings\Infrastructure\Persistence\Csv;

use Electricity\Readings\Domain\Reading;
use Electricity\Readings\Domain\Readings;
use Electricity\Readings\Domain\ReadingsRepository;
use League\Csv\Reader;

use function Lambdish\Phunctional\map;

final class CsvReadingsRepository implements ReadingsRepository
{
    private Reader $csvReader;

    public function __construct(private string $fileName)
    {
        $this->csvReader = Reader::createFromPath($this->fileName);
        $this->csvReader->setHeaderOffset(0);
    }

    public function all(): Readings
    {
        $readingsAsPrimitives = $this->csvReader->getRecords();

        return new Readings($this->readingsFromPrimitives($readingsAsPrimitives));
    }

    private function readingsFromPrimitives(\Iterator $readingsAsPrimitives): array
    {
        return map(
            fn(array $readingAsPrimitive): Reading => Reading::fromPrimitives(
                $readingAsPrimitive['client'],
                $readingAsPrimitive['period'],
                $readingAsPrimitive['reading'],
            ),
            $readingsAsPrimitives
        );
    }
}
