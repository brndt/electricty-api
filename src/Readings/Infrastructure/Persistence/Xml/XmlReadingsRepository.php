<?php

declare(strict_types=1);

namespace Electricity\Readings\Infrastructure\Persistence\Xml;

use Electricity\Readings\Domain\Readings;
use Electricity\Readings\Domain\ReadingsRepository;

final class XmlReadingsRepository implements ReadingsRepository
{
    public function all(): Readings
    {
        return new Readings([]);
    }
}
