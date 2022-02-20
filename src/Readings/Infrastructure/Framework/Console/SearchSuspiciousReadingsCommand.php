<?php

declare(strict_types=1);

namespace Electricity\Readings\Infrastructure\Framework\Console;

use Electricity\Readings\Application\SearchSuspiciousReadings\SearchSuspiciousReadingsApplicationService;
use Electricity\Readings\Application\SearchSuspiciousReadings\SuspiciousReadingResponse;
use Electricity\Readings\Infrastructure\Persistence\Csv\CsvReadingsRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use function Lambdish\Phunctional\map;

final class SearchSuspiciousReadingsCommand extends Command
{
    protected static $defaultName = 'electricity:search-suspicious-readings';

    protected function configure(): void
    {
        $this->addArgument('file-name', InputArgument::REQUIRED, 'The file name with data');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $fileName = $input->getArgument('file-name');

        $repository = new CsvReadingsRepository('%kernel.root_dir%/../src/Readings/Infrastructure/Persistence/Csv/2016-readings.csv');
        $applicationService = new SearchSuspiciousReadingsApplicationService($repository);
        $response = $applicationService();

        (new Table($output))
            ->setHeaders(['Client', 'Month', 'Suspicious', 'Median'])
            ->setRows(
                map(
                    fn(SuspiciousReadingResponse $response): array => [
                        $response->clientId,
                        $response->month,
                        $response->reading,
                        $response->median,
                    ],
                    $response->items()
                )
            )
            ->render();

        return Command::SUCCESS;
    }
}