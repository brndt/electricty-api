<?php

declare(strict_types=1);

namespace Electricity\Readings\Application\SearchSuspiciousReadings;

use Electricity\Common\Domain\Collection;

final class SuspiciousReadingCollectionResponse extends Collection
{
    public static function type(): string
    {
        return SuspiciousReadingResponse::class;
    }
}