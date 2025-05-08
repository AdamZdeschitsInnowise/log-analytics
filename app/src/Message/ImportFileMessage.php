<?php

declare(strict_types=1);

namespace App\Message;

class ImportFileMessage
{
    public function __construct(
        public readonly string $filePath
    ) {}
}
