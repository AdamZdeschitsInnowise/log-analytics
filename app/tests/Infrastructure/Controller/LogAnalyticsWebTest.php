<?php

declare(strict_types=1);

namespace App\Tests\Infrastructure\Controller;

use App\Entity\Log;
use App\Repository\LogRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * @coversNothing
 */
class LogAnalyticsWebTest extends WebTestCase
{
    private KernelBrowser $client;
    private LogRepository $repository;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->repository = static::getContainer()->get(LogRepository::class);

        $this->setupTestData();
    }

    public function testGetCountWithNoFilters(): void
    {
        $this->client->request('GET', '/count');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('Content-Type', 'application/json');

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        self::assertArrayHasKey('counter', $responseData);
        self::assertEquals(5, $responseData['counter']);
    }

    public function testGetCountWithServiceNameFilter(): void
    {
        $this->client->request('GET', '/count', ['serviceNames' => ['USER-SERVICE']]);

        self::assertResponseIsSuccessful();

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        self::assertArrayHasKey('counter', $responseData);
        self::assertEquals(3, $responseData['counter']);
    }

    public function testGetCountWithStatusCodeFilter(): void
    {
        $this->client->request('GET', '/count', ['statusCode' => 400]);

        self::assertResponseIsSuccessful();

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        self::assertArrayHasKey('counter', $responseData);
        self::assertEquals(2, $responseData['counter']);
    }

    public function testGetCountWithDateFilter(): void
    {
        $this->client->request('GET', '/count', [
            'startDate' => '2018-08-18T00:00:00Z',
        ]);

        self::assertResponseIsSuccessful();

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        self::assertArrayHasKey('counter', $responseData);
        self::assertEquals(2, $responseData['counter']);
    }

    public function testGetCountWithMultipleFilters(): void
    {
        $this->client->request('GET', '/count', [
            'serviceNames' => ['USER-SERVICE'],
            'statusCode' => 201,
            'startDate' => '2018-08-17T00:00:00Z',
            'endDate' => '2018-08-17T23:59:59Z',
        ]);

        self::assertResponseIsSuccessful();

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        self::assertArrayHasKey('counter', $responseData);
        self::assertEquals(1, $responseData['counter']);
    }

    public function testGetCountWithInvalidParameters(): void
    {
        $this->client->request('GET', '/count', [
            'startDate' => 'invalid-date',
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }

    private function setupTestData(): void
    {
        $entityManager = static::getContainer()->get('doctrine')->getManager();

        $entityManager->createQuery('DELETE FROM App\Entity\Log')->execute();

        $logs = [
            $this->createLog('USER-SERVICE', new \DateTime('2018-08-17 09:21:53'), 'POST /users HTTP/1.1', 201),
            $this->createLog('USER-SERVICE', new \DateTime('2018-08-17 09:21:54'), 'POST /users HTTP/1.1', 400),
            $this->createLog('INVOICE-SERVICE', new \DateTime('2018-08-17 09:21:55'), 'POST /invoices HTTP/1.1', 201),
            $this->createLog('USER-SERVICE', new \DateTime('2018-08-18 09:30:54'), 'POST /users HTTP/1.1', 400),
            $this->createLog('INVOICE-SERVICE', new \DateTime('2018-08-18 10:26:53'), 'POST /invoices HTTP/1.1', 201),
        ];

        foreach ($logs as $log) {
            $entityManager->persist($log);
        }

        $entityManager->flush();
    }

    private function createLog(string $serviceName, \DateTime $timestamp, string $requestInfo, int $statusCode): Log
    {
        $log = new Log();
        $log->setServiceName($serviceName);
        $log->setTimestamp($timestamp);
        $log->setRequestInfo($requestInfo);
        $log->setStatusCode($statusCode);

        return $log;
    }
}
