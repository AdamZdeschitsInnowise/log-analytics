<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\Criteria\CountSearchCriteria;
use App\Repository\LogRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Annotation\Route;

class LogAnalyticsController extends AbstractController
{
    public function __construct(
        private readonly LogRepository $repository,
    ) {}

    #[Route('/count', name: 'log_count', methods: ['GET'])]
    public function count(
        #[MapQueryString(validationFailedStatusCode: 400)]
        CountSearchCriteria $criteria
        = new CountSearchCriteria()
    ): JsonResponse {
        try {
            return $this->json(
                ['counter' => $this->repository->getCount($criteria)]
            );
        } catch (\Exception $e) {
            return $this->json(
                ['error' => 'Something went wrong while processing request. Please check logs.',
                    'exception' => $e],
                500
            );
        }
    }
}
