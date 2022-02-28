<?php

declare(strict_types=1);

namespace Electricity\Common\Infrastructure\File;

use Symfony\Component\HttpFoundation\File\File;

final class FileFactory
{
    /**
     * @throws FileExtensionIsNotDefinedException
     */
    public static function createFromFilePath(string $filePath): File
    {
        $file = new File($filePath);
        self::ensureFileExtensionExists($file);
        return $file;
    }

    private static function ensureFileExtensionExists(File $file): void
    {
        if ('' === $file->getExtension()) {
            throw new FileExtensionIsNotDefinedException();
        }
    }
}