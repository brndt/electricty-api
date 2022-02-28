<?php

declare(strict_types=1);

namespace Electricity\Readings\Infrastructure\Persistence;

use Electricity\Readings\Domain\ReadingsRepository;
use Electricity\Readings\Infrastructure\Persistence\Csv\CsvReadingsRepository;
use Electricity\Readings\Infrastructure\Persistence\Exception\RepositoryIsNotFoundForAnExtension;
use Electricity\Readings\Infrastructure\Persistence\Xml\XmlReadingsRepository;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;

final class ReadingsRepositoryFactory
{
    /**
     * @throws RepositoryIsNotFoundForAnExtension
     */
    public function fromFile(File $file): ReadingsRepository
    {
        return match ($file->getExtension()) {
            'csv' => new CsvReadingsRepository($file, new CsvEncoder()),
            'xml' => new XmlReadingsRepository($file, new XmlEncoder()),
            default => throw new RepositoryIsNotFoundForAnExtension(),
        };
    }
}