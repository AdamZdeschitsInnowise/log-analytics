<?php

declare(strict_types=1);

namespace App\Service\LogParser\Service;

use App\Service\LogParser\EntryStreamer\StreamerInterface;

final readonly class LogParserService implements LogParserServiceInterface
{
    public function __construct(
        private StreamerInterface $streamer,
        private LogParserRepositoryInterface $logParserRepository,
    ) {}

    public function parseLogFile(string $pathname, int $batchSize): void
    {
        $batch = [];

        foreach ($this->streamer->fileToEntries($pathname) as $entry) {
            $batch[] = $entry;

            if (count($batch) === $batchSize) {
                $this->logParserRepository->store(...$batch);
                $batch = [];
            }
        }

        if (count($batch) > 0) {
            $this->logParserRepository->store(...$batch);
        }
    }
}
