<?php

declare(strict_types=1);

namespace Electricity\Readings\Infrastructure\Persistence;

use Electricity\Common\Infrastructure\File\FileExtensionIsNotDefinedException;
use Electricity\Readings\Domain\ReadingsRepository;
use Electricity\Readings\Infrastructure\Persistence\Csv\CsvReadingsRepository;
use Electricity\Readings\Infrastructure\Persistence\Exception\RepositoryIsNotFoundForAnExtension;
use Symfony\Component\HttpFoundation\File\File;

final class ReadingsRepositoryFactory
{
    public static function fromFile(File $file): ReadingsRepository
    {
        return match ($file->getExtension()) {
            'csv' => new CsvReadingsRepository($file),
            default => throw new RepositoryIsNotFoundForAnExtension(),
        };
    }
}