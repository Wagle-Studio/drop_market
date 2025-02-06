<?php

namespace App\Entity;

use App\Entity\Traits\Timestampable;
use App\Repository\StatusWaveRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StatusWaveRepository::class)]
class StatusWave
{
    use Timestampable;

    public const DRAFT = "DRAFT";
    public const PUBLISHED = "PUBLISHED";
    public const REGISTRATION_OPEN = "REGISTRATION_OPEN";
    public const REGISTRATION_CLOSE = "REGISTRATION_CLOSE";
    public const LAUNCHED = "LAUNCHED";
    public const CLOSE = "CLOSE";

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 150)]
    private ?string $title = null;

    #[ORM\Column(length: 150)]
    private ?string $const = null;

    /**
     * @var Collection<int, Wave>
     */
    #[ORM\OneToMany(targetEntity: Wave::class, mappedBy: 'status', orphanRemoval: true)]
    private Collection $waves;

    public function __construct()
    {
        $this->waves = new ArrayCollection();
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getConst(): ?string
    {
        return $this->const;
    }

    public function setConst(string $const): static
    {
        $this->const = $const;

        return $this;
    }

    /**
     * @return Collection<int, Wave>
     */
    public function getWaves(): Collection
    {
        return $this->waves;
    }

    public function addWave(Wave $wave): static
    {
        if (!$this->waves->contains($wave)) {
            $this->waves->add($wave);
            $wave->setStatus($this);
        }

        return $this;
    }

    public function removeWave(Wave $wave): static
    {
        if ($this->waves->removeElement($wave)) {
            // set the owning side to null (unless already changed)
            if ($wave->getStatus() === $this) {
                $wave->setStatus(null);
            }
        }

        return $this;
    }
}
