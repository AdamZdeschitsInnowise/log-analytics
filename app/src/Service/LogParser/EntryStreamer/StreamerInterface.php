<?php

declare(strict_types=1);

namespace App\Service\LogParser\EntryStreamer;

use App\Service\LogParser\Parser\Entry;

interface StreamerInterface
{
    /**
     * @return iterable<Entry>
     */
    public function fileToEntries(string $pathname): iterable;
}
