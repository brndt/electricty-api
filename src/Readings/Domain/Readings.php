<?php

declare(strict_types=1);

namespace Electricity\Readings\Domain;

use Electricity\Common\Domain\Collection;

final class Readings extends Collection
{
    public static function type(): string
    {
        return Reading::class;
    }
}