<?php

namespace App\Entity;

use App\Repository\RobotGroupRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RobotGroupRepository::class)]
class RobotGroup
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'robotGroups')]
    #[ORM\JoinColumn(nullable: false)]
    private ?PuduAccount $puduAccount = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $puduGroupId = null;

    #[ORM\Column(length: 255)]
    private ?string $groupName = null;

    #[ORM\Column]
    private ?int $puduShopId = null;

    #[ORM\Column(length: 255)]
    private ?string $puduShopName = null;

    /**
     * @var Collection<int, RobotInGroup>
     */
    #[ORM\OneToMany(targetEntity: RobotInGroup::class, mappedBy: 'robotGroup')]
    private Collection $robotInGroups;

    public function __construct()
    {
        $this->robotInGroups = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getPuduGroupId(): ?string
    {
        return $this->puduGroupId;
    }

    public function setPuduGroupId(string $puduGroupId): static
    {
        $this->puduGroupId = $puduGroupId;

        return $this;
    }

    public function getGroupName(): ?string
    {
        return $this->groupName;
    }

    public function setGroupName(string $groupName): static
    {
        $this->groupName = $groupName;

        return $this;
    }

    public function getPuduShopId(): ?int
    {
        return $this->puduShopId;
    }

    public function setPuduShopId(int $puduShopId): static
    {
        $this->puduShopId = $puduShopId;

        return $this;
    }

    public function getPuduShopName(): ?string
    {
        return $this->puduShopName;
    }

    public function setPuduShopName(string $puduShopName): static
    {
        $this->puduShopName = $puduShopName;

        return $this;
    }

    /**
     * @return Collection<int, RobotInGroup>
     */
    public function getRobotInGroups(): Collection
    {
        return $this->robotInGroups;
    }

    public function addRobotInGroup(RobotInGroup $robotInGroup): static
    {
        if (!$this->robotInGroups->contains($robotInGroup)) {
            $this->robotInGroups->add($robotInGroup);
            $robotInGroup->setRobotGroup($this);
        }

        return $this;
    }

    public function removeRobotInGroup(RobotInGroup $robotInGroup): static
    {
        if ($this->robotInGroups->removeElement($robotInGroup)) {
            // set the owning side to null (unless already changed)
            if ($robotInGroup->getRobotGroup() === $this) {
                $robotInGroup->setRobotGroup(null);
            }
        }

        return $this;
    }
}
