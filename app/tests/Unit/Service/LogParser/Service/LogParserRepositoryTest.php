<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\LogParser\Service;

use App\Entity\Log;
use App\Service\LogParser\Parser\Entry;
use App\Service\LogParser\Service\LogParserRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(LogParserRepository::class)]
class LogParserRepositoryTest extends TestCase
{
    private EntityManagerInterface $entityManager;
    private LogParserRepository $repository;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->repository = new LogParserRepository($this->entityManager);
    }

    public function testStore(): void
    {
        $entries = [
            new Entry(
                'USER-SERVICE',
                \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', '2018-08-17 09:21:56'),
                'POST /users HTTP/1.1',
                201
            ),
            new Entry(
                'AUTH-SERVICE',
                \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', '2018-08-17 09:22:56'),
                'GET /auth HTTP/1.1',
                200
            ),
        ];

        $expectedLogs = [];
        foreach ($entries as $entry) {
            $log = new Log();
            $log->setServiceName($entry->getServiceName());
            $log->setTimestamp(\DateTime::createFromInterface($entry->getTimestamp()));
            $log->setRequestInfo($entry->getRequestData());
            $log->setStatusCode($entry->getStatusCode());
            $expectedLogs[] = $log;
        }

        $logIndex = 0;
        $this->entityManager->expects($this->exactly(2))
            ->method('persist')
            ->willReturnCallback(function (Log $log) use ($expectedLogs, &$logIndex) {
                $expectedLog = $expectedLogs[$logIndex];
                $this->assertEquals($expectedLog->getServiceName(), $log->getServiceName());
                $this->assertEquals(
                    $expectedLog->getTimestamp()->format('Y-m-d H:i:s'),
                    $log->getTimestamp()->format('Y-m-d H:i:s')
                );
                $this->assertEquals($expectedLog->getRequestInfo(), $log->getRequestInfo());
                $this->assertEquals($expectedLog->getStatusCode(), $log->getStatusCode());
                ++$logIndex;
            })
        ;

        $this->entityManager->expects($this->once())
            ->method('flush')
        ;

        $this->repository->store(...$entries);
    }
}
