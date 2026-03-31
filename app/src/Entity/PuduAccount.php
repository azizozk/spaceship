<?php

namespace App\Entity;

use App\Repository\PuduAccountRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PuduAccountRepository::class)]
class PuduAccount
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'puduAccounts')]
    private Collection $owners;

    #[ORM\Column(length: 255)]
    private ?string $apiKey = null;

    #[ORM\Column(length: 255)]
    private ?string $apiSecret = null;

    #[ORM\Column(length: 255)]
    private ?string $apiHost = null;

    public function __construct()
    {
        $this->owners = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, User>
     */
    public function getOwners(): Collection
    {
        return $this->owners;
    }

    public function addOwner(User $owner): static
    {
        if (!$this->owners->contains($owner)) {
            $this->owners->add($owner);
        }

        return $this;
    }

    public function removeOwner(User $owner): static
    {
        $this->owners->removeElement($owner);

        return $this;
    }

    public function getApiKey(): ?string
    {
        return $this->apiKey;
    }

    public function setApiKey(string $apiKey): static
    {
        $this->apiKey = $apiKey;

        return $this;
    }

    public function getApiSecret(): ?string
    {
        return $this->apiSecret;
    }

    public function setApiSecret(string $apiSecret): static
    {
        $this->apiSecret = $apiSecret;

        return $this;
    }

    public function getApiHost(): ?string
    {
        return $this->apiHost;
    }

    public function setApiHost(string $apiHost): static
    {
        $this->apiHost = $apiHost;

        return $this;
    }
}
