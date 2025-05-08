<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\LogParser\EntryStreamer;

use App\Service\LogParser\EntryStreamer\Streamer;
use App\Service\LogParser\Filesystem\LogReaderInterface;
use App\Service\LogParser\Parser\Entry;
use App\Service\LogParser\Parser\ParserException;
use App\Service\LogParser\Parser\ParserStrategyInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

#[CoversClass(Streamer::class)]
class StreamerTest extends TestCase
{
    private LogReaderInterface $logReader;
    private ParserStrategyInterface $parser;
    private LoggerInterface $logger;
    private Streamer $streamer;

    protected function setUp(): void
    {
        $this->logReader = $this->createMock(LogReaderInterface::class);
        $this->parser = $this->createMock(ParserStrategyInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->streamer = new Streamer($this->logReader, $this->parser, $this->logger);
    }

    public function testFileToEntriesSuccess(): void
    {
        $lines = [
            'USER-SERVICE - - [17/Aug/2018:09:21:56 +0000] "POST /users HTTP/1.1" 201',
            'AUTH-SERVICE - - [17/Aug/2018:09:22:56 +0000] "GET /auth HTTP/1.1" 200',
        ];

        $expectedEntries = [
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

        $this->logReader->expects($this->once())
            ->method('lines')
            ->with('test.log')
            ->willReturn($lines)
        ;

        $this->parser->expects($this->exactly(2))
            ->method('parse')
            ->willReturnOnConsecutiveCalls($expectedEntries[0], $expectedEntries[1])
        ;

        $entries = iterator_to_array($this->streamer->fileToEntries('test.log'));

        $this->assertCount(2, $entries);
        $this->assertEquals($expectedEntries[0], $entries[0]);
        $this->assertEquals($expectedEntries[1], $entries[1]);
    }

    public function testFileToEntriesWithParserError(): void
    {
        $lines = [
            'VALID-LINE - - [17/Aug/2018:09:21:56 +0000] "POST /users HTTP/1.1" 201',
            'INVALID-LINE',
        ];

        $expectedEntry = new Entry(
            'VALID-LINE',
            \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', '2018-08-17 09:21:56'),
            'POST /users HTTP/1.1',
            201
        );

        $this->logReader->expects($this->once())
            ->method('lines')
            ->with('test.log')
            ->willReturn($lines)
        ;

        $this->parser->expects($this->exactly(2))
            ->method('parse')
            ->willReturnCallback(function ($line) use ($expectedEntry) {
                if ('INVALID-LINE' === $line) {
                    throw new ParserException('Invalid line format');
                }

                return $expectedEntry;
            })
        ;

        $entries = iterator_to_array($this->streamer->fileToEntries('test.log'));

        $this->assertCount(1, $entries);
        $this->assertEquals($expectedEntry, $entries[0]);
    }
}
