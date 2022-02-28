<?php

declare(strict_types=1);

namespace Electricity\Common\Domain;

use InvalidArgumentException;
use Ramsey\Uuid\Uuid as RamseyUuid;
use Stringable;

abstract class Uuid implements Stringable
{
    public function __construct(public readonly string $value)
    {
        $this->ensureIsValidUuid($this->value);
    }

    private function ensureIsValidUuid(string $value): void
    {
        if (!RamseyUuid::isValid($value)) {
            throw new InvalidArgumentException($value);
        }
    }

    public static function random(): static
    {
        return new static(RamseyUuid::uuid4()->toString());
    }

    public function __toString(): string
    {
        return $this->value;
    }
}