<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\LogParser\Filesystem;

use App\Repository\LogFileRepositoryInterface;
use App\Service\LogParser\Filesystem\LogReader;
use App\Service\LogParser\Filesystem\LogReaderException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(LogReader::class)]
class LogReaderTest extends TestCase
{
    private LogFileRepositoryInterface $repository;
    private string $testFile;
    private LogReader $reader;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(LogFileRepositoryInterface::class);
        $this->testFile = tempnam(sys_get_temp_dir(), 'test_log_');
        $this->reader = new LogReader($this->repository, 2); // Small batch size for testing
    }

    protected function tearDown(): void
    {
        if (file_exists($this->testFile)) {
            unlink($this->testFile);
        }
    }

    public function testReadLinesInBatches(): void
    {
        $content = "Line 1\nLine 2\nLine 3\nLine 4\nLine 5";
        file_put_contents($this->testFile, $content);

        $this->repository->expects($this->once())
            ->method('getOffset')
            ->with($this->testFile)
            ->willReturn(0)
        ;

        $this->repository->expects($this->exactly(3))
            ->method('saveOffset')
        ;

        $counter = 1;
        foreach ($this->reader->lines($this->testFile) as $line) {
            if (5 !== $counter) {
                $this->assertEquals("Line {$counter}\n", $line);
            } else {
                $this->assertEquals("Line {$counter}", $line);
            }
            ++$counter;
        }
    }

    public function testFileDoesNotExist(): void
    {
        $this->expectException(LogReaderException::class);
        $this->expectExceptionMessage('Log file "nonexistent.log" does not exist.');

        iterator_to_array($this->reader->lines('nonexistent.log'));
    }

    public function testFileNotReadable(): void
    {
        file_put_contents($this->testFile, 'test');
        chmod($this->testFile, 0000);

        $this->expectException(LogReaderException::class);
        $this->expectExceptionMessage(sprintf('Log file "%s" is not readable.', $this->testFile));

        iterator_to_array($this->reader->lines($this->testFile));
    }

    public function testFailedToSeek(): void
    {
        file_put_contents($this->testFile, 'test');

        $this->repository->expects($this->once())
            ->method('getOffset')
            ->with($this->testFile)
            ->willReturn(100)
        ;

        $this->expectException(LogReaderException::class);
        $this->expectExceptionMessage(sprintf('Failed to read file "%s" at position 100.', $this->testFile));

        iterator_to_array($this->reader->lines($this->testFile));
    }
}
