<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'log_file')]
class LogFile
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    private string $path;

    #[ORM\Column(name: 'file_offset', type: 'bigint')]
    private int $fileOffset = 0;

    public function __construct(string $path)
    {
        $this->path = $path;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getFileOffset(): int
    {
        return $this->fileOffset;
    }

    public function setFileOffset(int $fileOffset): static
    {
        $this->fileOffset = $fileOffset;

        return $this;
    }
}
