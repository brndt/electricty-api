<?php

declare(strict_types=1);

namespace Electricity\Readings\Infrastructure\Persistence\Exception;

use Exception;

final class RepositoryIsNotFoundForAnExtension extends Exception
{
    protected $message = 'Repository is not found for an extension';
}