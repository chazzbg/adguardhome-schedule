<?php

namespace App\Entity;

use App\Repository\TraceRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TraceRepository::class)]
class Trace
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'traces')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Rule $rule = null;

    #[ORM\Column]
    private array $services = [];

    #[ORM\Column]
    private array $clients = [];

    #[ORM\Column]
    private array $servers = [];

    #[ORM\Column]
    private array $result = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getRule(): ?Rule
    {
        return $this->rule;
    }

    public function setRule(?Rule $rule): self
    {
        $this->rule = $rule;

        return $this;
    }

    public function getServices(): array
    {
        return $this->services;
    }

    public function setServices(array $services): self
    {
        $this->services = $services;

        return $this;
    }

    public function getClients(): array
    {
        return $this->clients;
    }

    public function setClients(array $clients): self
    {
        $this->clients = $clients;

        return $this;
    }

    public function getServers(): array
    {
        return $this->servers;
    }

    public function setServers(array $servers): self
    {
        $this->servers = $servers;

        return $this;
    }

    public function getResult(): array
    {
        return $this->result;
    }

    public function setResult(array $result): self
    {
        $this->result = $result;

        return $this;
    }
}
