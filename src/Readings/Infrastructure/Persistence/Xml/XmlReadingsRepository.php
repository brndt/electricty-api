<?php

declare(strict_types=1);

namespace Electricity\Readings\Infrastructure\Persistence\Xml;

use Electricity\Readings\Domain\ReadingByPeriod;
use Electricity\Readings\Domain\ReadingsRepository;

final class XmlReadingsRepository implements ReadingsRepository
{
    public function all(): array
    {
        return [];
    }
}
