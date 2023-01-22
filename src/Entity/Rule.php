<?php

namespace App\Entity;

use App\ApiClient\ServicesEnum;
use App\Repository\RuleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: RuleRepository::class)]
class Rule
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\Expression("this.isDayOfWeek() == true", message: "Select at least one day of week")]
    #[ORM\Column(nullable: true)]
    private ?bool $monday = null;

    #[Assert\Expression("this.isDayOfWeek() == true", message: "Select at least one day of week")]
    #[ORM\Column(nullable: true)]
    private ?bool $tuesday = null;

    #[Assert\Expression("this.isDayOfWeek() == true", message: "Select at least one day of week")]
    #[ORM\Column(nullable: true)]
    private ?bool $wednesday = null;

    #[Assert\Expression("this.isDayOfWeek() == true", message: "Select at least one day of week")]
    #[ORM\Column(nullable: true)]
    private ?bool $thursday = null;

    #[Assert\Expression("this.isDayOfWeek() == true", message: "Select at least one day of week")]
    #[ORM\Column(nullable: true)]
    private ?bool $friday = null;

    #[Assert\Expression("this.isDayOfWeek() == true", message: "Select at least one day of week")]
    #[ORM\Column(nullable: true)]
    private ?bool $saturday = null;

    #[Assert\Expression("this.isDayOfWeek() == true", message: "Select at least one day of week")]
    #[ORM\Column(nullable: true)]
    private ?bool $sunday = null;



    #[ORM\Column(type: Types::JSON, nullable: true)]
    #[Assert\Choice(choices: ServicesEnum::ALL, multiple: true)]
    #[Assert\NotBlank]
    private array $services = [];

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private array $clients = [];

    #[ORM\ManyToMany(targetEntity: Server::class, inversedBy: 'rules')]
    #[Assert\NotNull]
    #[Assert\Count(min: 1, minMessage: "Select at least one server")]
    private Collection $servers;

    #[ORM\Column]
    private ?bool $enabled = null;

    #[ORM\OneToMany(mappedBy: 'rule', targetEntity: Trace::class, orphanRemoval: true)]
    private Collection $traces;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $appliedAt = null;

    #[ORM\Column(type: Types::TIME_IMMUTABLE, nullable: true)]
    #[Assert\NotBlank]
    #[Assert\NotNull]
    private ?\DateTimeImmutable $blockAt = null;

    #[ORM\Column(type: Types::TIME_IMMUTABLE, nullable: true)]
    #[Assert\NotBlank]
    #[Assert\NotNull]
    private ?\DateTimeImmutable $unblockAt = null;

    public function __construct()
    {
        $this->servers = new ArrayCollection();
        $this->traces = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function isMonday(): ?bool
    {
        return $this->monday;
    }

    public function setMonday(bool $monday): self
    {
        $this->monday = $monday;

        return $this;
    }

    public function isTuesday(): ?bool
    {
        return $this->tuesday;
    }

    public function setTuesday(bool $tuesday): self
    {
        $this->tuesday = $tuesday;

        return $this;
    }

    public function isWednesday(): ?bool
    {
        return $this->wednesday;
    }

    public function setWednesday(bool $wednesday): self
    {
        $this->wednesday = $wednesday;

        return $this;
    }

    public function isThursday(): ?bool
    {
        return $this->thursday;
    }

    public function setThursday(bool $thursday): self
    {
        $this->thursday = $thursday;

        return $this;
    }

    public function isFriday(): ?bool
    {
        return $this->friday;
    }

    public function setFriday(bool $friday): self
    {
        $this->friday = $friday;

        return $this;
    }

    public function isSaturday(): ?bool
    {
        return $this->saturday;
    }

    public function setSaturday(bool $saturday): self
    {
        $this->saturday = $saturday;

        return $this;
    }

    public function isSunday(): ?bool
    {
        return $this->sunday;
    }

    public function setSunday(bool $sunday): self
    {
        $this->sunday = $sunday;

        return $this;
    }

    public function isDayOfWeek(): bool
    {
        return $this->monday || $this->tuesday || $this->wednesday || $this->thursday ||  $this->friday || $this->saturday || $this->sunday;
    }

    public function getServices(): array
    {
        return $this->services;
    }

    public function setServices(?array $services): self
    {
        $this->services = $services;

        return $this;
    }

    public function getClients(): array
    {
        return $this->clients;
    }

    public function setClients(?array $clients): self
    {
        $this->clients = $clients;

        return $this;
    }

    /**
     * @return Collection<int, Server>
     */
    public function getServers(): Collection
    {
        return $this->servers;
    }

    public function addServer(Server $server): self
    {
        if (!$this->servers->contains($server)) {
            $this->servers->add($server);
        }

        return $this;
    }

    public function removeServer(Server $server): self
    {
        $this->servers->removeElement($server);

        return $this;
    }

    public function isEnabled(): ?bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * @return Collection<int, Trace>
     */
    public function getTraces(): Collection
    {
        return $this->traces;
    }

    public function addTrace(Trace $trace): self
    {
        if (!$this->traces->contains($trace)) {
            $this->traces->add($trace);
            $trace->setRule($this);
        }

        return $this;
    }

    public function removeTrace(Trace $trace): self
    {
        if ($this->traces->removeElement($trace)) {
            // set the owning side to null (unless already changed)
            if ($trace->getRule() === $this) {
                $trace->setRule(null);
            }
        }

        return $this;
    }

    public function getAppliedAt(): ?\DateTimeInterface
    {
        return $this->appliedAt;
    }

    public function setAppliedAt(?\DateTimeInterface $appliedAt): self
    {
        $this->appliedAt = $appliedAt;

        return $this;
    }

    public function getBlockAt(): ?\DateTimeImmutable
    {
        return $this->blockAt;
    }

    public function setBlockAt(\DateTimeImmutable $blockAt): self
    {
        $this->blockAt = $blockAt;
        return $this;
    }

    public function getUnblockAt(): ?\DateTimeImmutable
    {
        return $this->unblockAt;
    }

    public function setUnblockAt(\DateTimeImmutable $unblockAt): self
    {
        $this->unblockAt = $unblockAt;

        return $this;
    }

}
