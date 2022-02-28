<?php

declare(strict_types=1);

namespace Electricity\Readings\Domain;

final class ClientWithReadings
{
    public function __construct(
        public readonly ClientId $clientId,
        public readonly ReadingsByPeriodCollection $readings,
    ) {
    }

    public function sortReadingsByAsc(): self
    {
        return new self($this->clientId, $this->readings->sortedByAsc());
    }
}