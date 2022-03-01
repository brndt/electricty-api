<?php

declare(strict_types=1);

namespace Electricity\Tests\Readings;

use Electricity\Readings\Domain\ClientWithReadings;
use Electricity\Readings\Domain\ReadingsRepository;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

abstract class ReadingsUnitTestCase extends TestCase
{
    private MockInterface|ReadingsRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = Mockery::mock(ReadingsRepository::class);
    }

    public function repository(): MockInterface|ReadingsRepository
    {
        return $this->repository;
    }

    protected function shouldReturnAll(ClientWithReadings ...$clientWithReadings): void
    {
        $this->repository()
            ->shouldReceive('all')
            ->once()
            ->andReturn($clientWithReadings)
        ;
    }
}