<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\LogRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LogRepository::class)]
#[ORM\Index(name: 'idx_serviceName', columns: ['service_name'])]
#[ORM\Index(name: 'idx_timestamp', columns: ['timestamp'])]
#[ORM\Index(name: 'idx_statusCode', columns: ['status_code'])]
class Log
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $serviceName = null;

    #[ORM\Column]
    private ?\DateTime $timestamp = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $requestInfo = null;

    #[ORM\Column]
    private ?int $statusCode = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getServiceName(): ?string
    {
        return $this->serviceName;
    }

    public function setServiceName(string $serviceName): static
    {
        $this->serviceName = $serviceName;

        return $this;
    }

    public function getTimestamp(): ?\DateTime
    {
        return $this->timestamp;
    }

    public function setTimestamp(\DateTime $timestamp): static
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    public function getRequestInfo(): ?string
    {
        return $this->requestInfo;
    }

    public function setRequestInfo(?string $requestInfo): static
    {
        $this->requestInfo = $requestInfo;

        return $this;
    }

    public function getStatusCode(): ?int
    {
        return $this->statusCode;
    }

    public function setStatusCode(int $statusCode): static
    {
        $this->statusCode = $statusCode;

        return $this;
    }
}
