<?php

declare(strict_types=1);

namespace Electricity\Readings\Domain;

final class ClientId
{
    public function __construct(public readonly string $value)
    {
    }
}