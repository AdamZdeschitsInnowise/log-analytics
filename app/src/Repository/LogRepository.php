<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Log;
use App\Repository\Criteria\CountSearchCriteria;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class LogRepository extends ServiceEntityRepository implements LogRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Log::class);
    }

    public function getCount(CountSearchCriteria $searchCriteria): int
    {
        return $this->matching($searchCriteria->toFilterCriteria())
            ->count()
        ;
    }
}
