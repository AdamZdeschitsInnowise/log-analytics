<?php

declare(strict_types=1);

namespace App\Service\LogParser\Service;

use App\Entity\Log;
use App\Service\LogParser\Parser\Entry;
use Doctrine\ORM\EntityManagerInterface;

class LogParserRepository implements LogParserRepositoryInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {}

    public function store(Entry ...$entries): void
    {
        foreach ($entries as $entry) {
            $log = new Log();
            $log->setServiceName($entry->getServiceName());
            $log->setTimestamp(\DateTime::createFromInterface($entry->getTimestamp()));
            $log->setRequestInfo($entry->getRequestData());
            $log->setStatusCode($entry->getStatusCode());

            $this->entityManager->persist($log);
        }

        $this->entityManager->flush();
    }
}
