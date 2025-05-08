<?php

declare(strict_types=1);

namespace App\Service\LogParser\Service;

interface LogParserServiceInterface
{
    public function parseLogFile(string $pathname, int $batchSize): void;
}
