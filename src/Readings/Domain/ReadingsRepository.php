<?php

declare(strict_types=1);

namespace Electricity\Readings\Domain;

interface ReadingsRepository
{
    public function all(): array;
}