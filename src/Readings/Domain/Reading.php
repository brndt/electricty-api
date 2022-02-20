<?php

declare(strict_types=1);

namespace Electricity\Readings\Domain;

final class Reading
{
    public function __construct(
        public readonly string $clientId,
        public readonly string $period,
        public readonly string $reading,
    ) {
    }

    public static function fromPrimitives(string $clientId, string $period, string $reading): Reading
    {
        return new self($clientId, $period, $reading);
    }
}