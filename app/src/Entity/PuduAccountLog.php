<?php

namespace App\Entity;

use App\Repository\PuduAccountLogRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PuduAccountLogRepository::class)]
class PuduAccountLog
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'puduAccountLogs')]
    #[ORM\JoinColumn(nullable: false)]
    private ?PuduAccount $puduAccount = null;

    #[ORM\Column(length: 8)]
    private ?string $method = null;

    #[ORM\Column(length: 255)]
    private ?string $uri = null;

    #[ORM\Column(nullable: true)]
    private ?array $body = null;

    #[ORM\Column(nullable: true)]
    private ?int $responseCode = null;

    #[ORM\Column(nullable: true)]
    private ?array $responseBody = null;

    #[ORM\Column]
    private \DateTimeImmutable $executedAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function __construct()
    {
        $this->executedAt = new \DateTimeImmutable();
    }

    public function getPuduAccount(): ?PuduAccount
    {
        return $this->puduAccount;
    }

    public function setPuduAccount(?PuduAccount $puduAccount): static
    {
        $this->puduAccount = $puduAccount;

        return $this;
    }

    public function getMethod(): ?string
    {
        return $this->method;
    }

    public function setMethod(string $method): static
    {
        $this->method = $method;

        return $this;
    }

    public function getUri(): ?string
    {
        return $this->uri;
    }

    public function setUri(string $uri): static
    {
        $this->uri = $uri;

        return $this;
    }

    public function getBody(): ?array
    {
        return $this->body;
    }

    public function setBody(?array $body): static
    {
        $this->body = $body;

        return $this;
    }

    public function getResponseCode(): ?int
    {
        return $this->responseCode;
    }

    public function setResponseCode(?int $responseCode): static
    {
        $this->responseCode = $responseCode;

        return $this;
    }

    public function getResponseBody(): ?array
    {
        return $this->responseBody;
    }

    public function setResponseBody(?array $responseBody): static
    {
        $this->responseBody = $responseBody;

        return $this;
    }

    public function getExecutedAt(): ?\DateTimeImmutable
    {
        return $this->executedAt;
    }

    public function setExecutedAt(\DateTimeImmutable $executedAt): static
    {
        $this->executedAt = $executedAt;

        return $this;
    }
}
