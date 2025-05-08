<?php

declare(strict_types=1);

namespace App\Service\LogParser\Filesystem;

use App\Repository\LogFileRepositoryInterface;

final readonly class LogReader implements LogReaderInterface
{
    public function __construct(
        private LogFileRepositoryInterface $repository,
        private int $batchSize = 100,
    ) {}

    public function lines(string $pathname): iterable
    {
        if (!file_exists($pathname)) {
            throw new LogReaderException(sprintf(
                'Log file "%s" does not exist.',
                $pathname,
            ));
        }

        if (!is_readable($pathname)) {
            throw new LogReaderException(sprintf(
                'Log file "%s" is not readable.',
                $pathname,
            ));
        }

        $file = fopen($pathname, 'rb');

        if (!$file) {
            throw new LogReaderException(sprintf(
                'Failed to open file "%s".',
                $pathname,
            ));
        }

        $offset = $this->repository->getOffset($pathname);

        if (0 !== fseek($file, $offset)) {
            throw new LogReaderException(sprintf(
                'Failed to skip "%s" to position %d.',
                $pathname,
                $offset,
            ));
        }

        $batch = [];

        while (!feof($file)) {
            $line = fgets($file);

            if (false === $line) {
                throw new LogReaderException(sprintf(
                    'Failed to read file "%s" at position %d.',
                    $pathname,
                    ftell($file),
                ));
            }

            $batch[] = $line;

            if (count($batch) === $this->batchSize) {
                yield from $batch;

                $this->repository->saveOffset($pathname, (int) ftell($file));
                $batch = [];
            }
        }

        if (count($batch) > 0) {
            yield from $batch;

            $this->repository->saveOffset($pathname, (int) ftell($file));
        }

        fclose($file);
    }
}
