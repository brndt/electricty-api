<?php

declare(strict_types=1);

namespace Electricity\Common\Infrastructure\File;

use Exception;

final class FileExtensionIsNotDefinedException extends Exception
{
    protected $message = 'File extension is not defined';
}