<?php

declare(strict_types=1);

namespace App\Service\LogParser\Parser;

final readonly class Entry
{
    public function __construct(
        private string $serviceName,
        private \DateTimeInterface $timestamp,
        private string $requestData,
        private int $statusCode,
    ) {}

    public function getTimestamp(): \DateTimeInterface
    {
        return $this->timestamp;
    }

    public function getRequestData(): string
    {
        return $this->requestData;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getServiceName(): string
    {
        return $this->serviceName;
    }
}
