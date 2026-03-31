<?php

namespace App\Entity;

use App\Repository\RobotInGroupRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RobotInGroupRepository::class)]
class RobotInGroup
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'robotInGroups')]
    #[ORM\JoinColumn(nullable: false)]
    private ?RobotGroup $robotGroup = null;

    #[ORM\Column(length: 255)]
    private ?string $mac = null;

    #[ORM\Column(length: 255)]
    private ?string $sn = null;

    #[ORM\Column(length: 255)]
    private ?string $robot_name = null;

    #[ORM\Column]
    private ?int $puduShopId = null;

    #[ORM\Column(length: 255)]
    private ?string $puduShopName = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRobotGroup(): ?RobotGroup
    {
        return $this->robotGroup;
    }

    public function setRobotGroup(?RobotGroup $robotGroup): static
    {
        $this->robotGroup = $robotGroup;

        return $this;
    }

    public function getMac(): ?string
    {
        return $this->mac;
    }

    public function setMac(string $mac): static
    {
        $this->mac = $mac;

        return $this;
    }

    public function getSn(): ?string
    {
        return $this->sn;
    }

    public function setSn(string $sn): static
    {
        $this->sn = $sn;

        return $this;
    }

    public function getRobotName(): ?string
    {
        return $this->robot_name;
    }

    public function setRobotName(string $robot_name): static
    {
        $this->robot_name = $robot_name;

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
}
