<?php

namespace App\Entity;

use App\Repository\DeveloperRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DeveloperRepository::class)
 */
class Developer
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="float")
     */
    private $work_capacity_per_hour;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getWorkCapacityPerHour(): ?float
    {
        return $this->work_capacity_per_hour;
    }

    public function setWorkCapacityPerHour(float $work_capacity_per_hour): self
    {
        $this->work_capacity_per_hour = $work_capacity_per_hour;

        return $this;
    }
}
