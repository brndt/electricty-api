<?php

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

use Electricity\Kernel;

return function (array $context) {
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
