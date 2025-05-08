<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\LogFile;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LogFile>
 */
class LogFileRepository extends ServiceEntityRepository implements LogFileRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LogFile::class);
    }

    public function getOffset(string $pathname): int
    {
        $logFile = $this->getEntityManager()->getRepository(LogFile::class)->findOneBy(['path' => $pathname]);

        return $logFile?->getFileOffset() ?? 0;
    }

    public function saveOffset(string $pathname, int $offset): void
    {
        $em = $this->getEntityManager();

        $logFile = $em->getRepository(LogFile::class)->findOneBy(['path' => $pathname]);

        if (!$logFile) {
            $logFile = new LogFile($pathname);
            $em->persist($logFile);
        }

        $logFile->setFileOffset($offset);

        $em->flush();
    }
}
