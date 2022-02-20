<?php

declare(strict_types=1);

namespace Electricity\Common\Domain\Criteria;

final class FilterValue
{
    public function __construct(public readonly string $value)
    {
    }
}
