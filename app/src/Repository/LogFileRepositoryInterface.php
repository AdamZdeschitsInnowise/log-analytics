<?php

declare(strict_types=1);

namespace App\Repository;

interface LogFileRepositoryInterface
{
    public function saveOffset(string $pathname, int $offset): void;

    public function getOffset(string $pathname): int;
}
