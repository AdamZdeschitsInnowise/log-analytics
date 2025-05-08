<?php

declare(strict_types=1);

namespace App\Service\LogParser\EntryStreamer;

use App\Service\LogParser\Filesystem\LogReaderInterface;
use App\Service\LogParser\Parser\Entry;
use App\Service\LogParser\Parser\ParserException;
use App\Service\LogParser\Parser\ParserStrategyInterface;
use Psr\Log\LoggerInterface;

final readonly class Streamer implements StreamerInterface
{
    public function __construct(
        private LogReaderInterface $logReader,
        private ParserStrategyInterface $parser,
        private LoggerInterface $logger,
    ) {}

    /**
     * @return iterable<Entry>
     */
    public function fileToEntries(string $pathname): iterable
    {
        foreach ($this->logReader->lines($pathname) as $line) {
            try {
                yield $this->parser->parse($line);
            } catch (ParserException $e) {
                $this->logger->warning($e->getMessage(), [
                    'line' => $line,
                    'exception' => $e,
                ]);
            }
        }
    }
}
