<?php

declare(strict_types=1);

namespace Electricity\Readings\Infrastructure\Framework\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class SearchSuspiciousReadingsCommand extends Command
{
    protected static $defaultName = 'electricity:search-suspicious-readings';

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        return Command::SUCCESS;
    }
}