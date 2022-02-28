<?php

declare(strict_types=1);

namespace Electricity\Readings\Infrastructure\Persistence\Xml;

use Electricity\Readings\Domain\ClientId;
use Electricity\Readings\Domain\ClientWithReadings;
use Electricity\Readings\Domain\ReadingByPeriod;
use Electricity\Readings\Domain\ReadingsRepository;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Encoder\XmlEncoder;

use function Lambdish\Phunctional\group_by;
use function Lambdish\Phunctional\map;

final class XmlReadingsRepository implements ReadingsRepository
{
    private mixed $readings;

    public function __construct(File $file, XmlEncoder $encoder)
    {
        $this->readings = $encoder->decode($file->getContent(), 'xml')['reading'];
    }

    public function all(): array
    {
        $readingsGroupedByClientId = group_by(fn($reading): string => $reading['@clientID'], $this->readings);

        return $this->readingsFromPrimitives($readingsGroupedByClientId);
    }

    private function readingsFromPrimitives(array $readingsGroupedByClientId): array
    {
        return map($this->clientWithReadingsFromPrimitives(), $readingsGroupedByClientId);
    }

    private function clientWithReadingsFromPrimitives(): \Closure
    {
        return fn(array $clientReadings, string $clientId): ClientWithReadings => new ClientWithReadings(
            new ClientId($clientId), $this->readingsByPeriodFromPrimitives($clientReadings),
        );
    }

    private function readingsByPeriodFromPrimitives(array $clientReadings): array
    {
        return map(fn(array $reading) => new ReadingByPeriod($reading['@period'], $reading['#']), $clientReadings);
    }
}
