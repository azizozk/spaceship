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

    /**
     * @var Collection<int, PuduAccountLog>
     */
    #[ORM\OneToMany(targetEntity: PuduAccountLog::class, mappedBy: 'puduAccount')]
    private Collection $puduAccountLogs;

    /**
     * @var Collection<int, RobotGroup>
     */
    #[ORM\OneToMany(targetEntity: RobotGroup::class, mappedBy: 'puduAccount')]
    private Collection $robotGroups;

    public function __construct()
    {
        $this->owners = new ArrayCollection();
        $this->puduAccountLogs = new ArrayCollection();
        $this->robotGroups = new ArrayCollection();
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

    /**
     * @return Collection<int, PuduAccountLog>
     */
    public function getPuduAccountLogs(): Collection
    {
        return $this->puduAccountLogs;
    }

    public function addPuduAccountLog(PuduAccountLog $puduAccountLog): static
    {
        if (!$this->puduAccountLogs->contains($puduAccountLog)) {
            $this->puduAccountLogs->add($puduAccountLog);
            $puduAccountLog->setPuduAccount($this);
        }

        return $this;
    }

    public function removePuduAccountLog(PuduAccountLog $puduAccountLog): static
    {
        if ($this->puduAccountLogs->removeElement($puduAccountLog)) {
            // set the owning side to null (unless already changed)
            if ($puduAccountLog->getPuduAccount() === $this) {
                $puduAccountLog->setPuduAccount(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, RobotGroup>
     */
    public function getRobotGroups(): Collection
    {
        return $this->robotGroups;
    }

    public function addRobotGroup(RobotGroup $robotGroup): static
    {
        if (!$this->robotGroups->contains($robotGroup)) {
            $this->robotGroups->add($robotGroup);
            $robotGroup->setPuduAccount($this);
        }

        return $this;
    }

    public function removeRobotGroup(RobotGroup $robotGroup): static
    {
        if ($this->robotGroups->removeElement($robotGroup)) {
            // set the owning side to null (unless already changed)
            if ($robotGroup->getPuduAccount() === $this) {
                $robotGroup->setPuduAccount(null);
            }
        }

        return $this;
    }
}
