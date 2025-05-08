<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Message\ImportFileMessage;
use App\Service\LogParser\Service\LogParserServiceInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class ImportFileMessageHandler
{
    public function __construct(
        private readonly LogParserServiceInterface $logParserService,
    ) {}

    public function __invoke(ImportFileMessage $logFilePath): void
    {
        try {
            $this->logParserService->parseLogFile($logFilePath->filePath, 100);
        } catch (\Exception $e) {
        }
    }
}
