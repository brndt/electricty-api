<?php

declare(strict_types=1);

namespace Electricity\Readings\Infrastructure\Framework\Console;

use Electricity\Common\Infrastructure\File\FileExtensionIsNotDefinedException;
use Electricity\Common\Infrastructure\File\FileFactory;
use Electricity\Readings\Application\SearchSuspiciousReadings\SearchSuspiciousReadingsApplicationService;
use Electricity\Readings\Application\SearchSuspiciousReadings\SuspiciousReadingResponse;
use Electricity\Readings\Infrastructure\Persistence\Exception\RepositoryIsNotFoundForAnExtension;
use Electricity\Readings\Infrastructure\Persistence\ReadingsRepositoryFactory;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;

use function Lambdish\Phunctional\map;

final class SearchSuspiciousReadingsCommand extends Command
{
    public function __construct(
        private readonly ReadingsRepositoryFactory $readingsRepositoryFactory,
        private readonly FileFactory $fileFactory,
    ) {
        parent::__construct();
    }
    private const FILE_PATH = __DIR__ . '/../../Persistence/Data/';

    protected static $defaultName = 'electricity:search-suspicious-readings';

    protected function configure(): void
    {
        $this->addArgument('file-name', InputArgument::REQUIRED, 'The file name with data');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $file = $this->fileFactory::createFromFilePath(self::FILE_PATH . $input->getArgument('file-name'));
        } catch (FileNotFoundException | FileExtensionIsNotDefinedException $exception) {
            $output->writeln('<error>' . $exception->getMessage() . '</error>');
            return Command::FAILURE;
        }

        try {
            $repository = $this->readingsRepositoryFactory::fromFile($file);
        } catch (Exception $exception) {
            $output->writeln('<error>' . $exception->getMessage() . '</error>');
            return Command::FAILURE;
        }

        $response = (new SearchSuspiciousReadingsApplicationService($repository))();

        if (0 === $response->count()) {
            $output->writeln('<error>Suspicious readings don\'t exist</error>');
            return Command::FAILURE;
        }

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