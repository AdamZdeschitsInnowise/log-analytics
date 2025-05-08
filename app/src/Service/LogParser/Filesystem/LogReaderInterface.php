<?php

declare(strict_types=1);

namespace App\Service\LogParser\Filesystem;

interface LogReaderInterface
{
    /**
     * @return iterable<string>
     *
     * @throws LogReaderException
     */
    public function lines(string $pathname): iterable;
}
