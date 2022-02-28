<?php

declare(strict_types=1);

namespace Electricity\Readings\Domain;

use function Lambdish\Phunctional\sort;

final class ClientWithReadings
{
    public function __construct(
        public readonly ClientId $clientId,
        public readonly array $readings,
    ) {
    }

    public function sortReadingsByAsc(): self
    {
        return new self($this->clientId, sort(ReadingByPeriod::comparator(), $this->readings));
    }
}