<?php

declare(strict_types=1);

namespace App\Service\LogParser\Service;

use App\Service\LogParser\Parser\Entry;

interface LogParserRepositoryInterface
{
    public function store(Entry ...$entries): void;
}
