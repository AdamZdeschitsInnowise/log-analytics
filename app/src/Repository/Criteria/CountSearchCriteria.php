<?php

declare(strict_types=1);

namespace App\Repository\Criteria;

use Doctrine\Common\Collections\Criteria;
use Symfony\Component\Validator\Constraints as Assert;

class CountSearchCriteria
{
    /**
     * @param null|array<string> $serviceNames
     */
    public function __construct(
        #[Assert\All([new Assert\Type('string')])]
        public ?array $serviceNames = null,
        #[Assert\DateTime(format: \DateTimeInterface::ATOM)]
        public ?string $startDate = null,
        #[Assert\DateTime(format: \DateTimeInterface::ATOM)]
        public ?string $endDate = null,
        #[Assert\Type('integer')]
        public ?int $statusCode = null,
    ) {}

    public function toFilterCriteria(): Criteria
    {
        $criteria = Criteria::create();

        if (null !== $this->serviceNames) {
            $criteria->andWhere(
                Criteria::expr()->in('serviceName', $this->serviceNames)
            );
        }

        if (null !== $this->startDate) {
            $criteria->andWhere(
                Criteria::expr()->gte('timestamp', \DateTime::createFromFormat(\DateTimeInterface::ATOM, $this->startDate))
            );
        }

        if (null !== $this->endDate) {
            $criteria->andWhere(
                Criteria::expr()->lte('timestamp', \DateTime::createFromFormat(\DateTimeInterface::ATOM, $this->endDate))
            );
        }

        if (null !== $this->statusCode) {
            $criteria->andWhere(
                Criteria::expr()->eq('statusCode', $this->statusCode)
            );
        }

        return $criteria;
    }
}
