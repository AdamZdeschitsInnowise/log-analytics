<?php

declare(strict_types=1);

namespace App\Repository;

use App\Repository\Criteria\CountSearchCriteria;

interface LogRepositoryInterface
{
    public function getCount(CountSearchCriteria $searchCriteria): int;
}
