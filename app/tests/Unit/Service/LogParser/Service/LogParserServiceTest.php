<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\LogParser\Service;

use App\Service\LogParser\EntryStreamer\StreamerInterface;
use App\Service\LogParser\Parser\Entry;
use App\Service\LogParser\Service\LogParserRepositoryInterface;
use App\Service\LogParser\Service\LogParserService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(LogParserService::class)]
class LogParserServiceTest extends TestCase
{
    private StreamerInterface $streamer;
    private LogParserRepositoryInterface $repository;
    private LogParserService $service;

    protected function setUp(): void
    {
        $this->streamer = $this->createMock(StreamerInterface::class);
        $this->repository = $this->createMock(LogParserRepositoryInterface::class);
        $this->service = new LogParserService($this->streamer, $this->repository);
    }

    public function testParseLogFileWithFullBatch(): void
    {
        $entries = [
            new Entry(
                'USER-SERVICE',
                \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', '2018-08-17 09:21:56'),
                'POST /users HTTP/1.1',
                201
            ),
            new Entry(
                'AUTH-SERVICE',
                \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', '2018-08-17 09:22:56'),
                'GET /auth HTTP/1.1',
                200
            ),
        ];

        $this->streamer->expects($this->once())
            ->method('fileToEntries')
            ->with('test.log')
            ->willReturn($entries)
        ;

        $this->repository->expects($this->once())
            ->method('store')
            ->with(...$entries)
        ;

        $this->service->parseLogFile('test.log', 2);
    }

    public function testParseLogFileWithPartialBatch(): void
    {
        $entries = [
            new Entry(
                'USER-SERVICE',
                \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', '2018-08-17 09:21:56'),
                'POST /users HTTP/1.1',
                201
            ),
            new Entry(
                'AUTH-SERVICE',
                \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', '2018-08-17 09:22:56'),
                'GET /auth HTTP/1.1',
                200
            ),
            new Entry(
                'PAYMENT-SERVICE',
                \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', '2018-08-17 09:23:56'),
                'POST /payment HTTP/1.1',
                201
            ),
        ];

        $this->streamer->expects($this->once())
            ->method('fileToEntries')
            ->with('test.log')
            ->willReturn($entries)
        ;

        $expectedBatches = [
            [$entries[0], $entries[1]],
            [$entries[2]],
        ];
        $batchIndex = 0;

        $this->repository->expects($this->exactly(2))
            ->method('store')
            ->willReturnCallback(function (...$args) use ($expectedBatches, &$batchIndex) {
                $this->assertEquals($expectedBatches[$batchIndex], $args);
                ++$batchIndex;
            })
        ;

        $this->service->parseLogFile('test.log', 2);
    }

    public function testParseLogFileWithEmptyFile(): void
    {
        $this->streamer->expects($this->once())
            ->method('fileToEntries')
            ->with('test.log')
            ->willReturn([])
        ;

        $this->repository->expects($this->never())
            ->method('store')
        ;

        $this->service->parseLogFile('test.log', 2);
    }
}
