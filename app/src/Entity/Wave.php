<?php

namespace App\Entity;

use App\Entity\Traits\Timestampable;
use App\Entity\Traits\Ulidable;
use App\Repository\WaveRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: WaveRepository::class)]
#[ORM\Table(name: "`wave`")]
class Wave
{
    use Timestampable;
    use Ulidable;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank(message: "Champs date et heure requis.")]
    #[Assert\GreaterThan(
        value: "now",
        message: "La date et l'heure doivent être supérieures à maintenant."
    )]
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $start = null;

    #[ORM\ManyToOne(inversedBy: "waves")]
    #[ORM\JoinColumn(nullable: false)]
    private ?Shop $shop = null;

    #[ORM\ManyToOne(inversedBy: "waves")]
    #[ORM\JoinColumn(nullable: false)]
    private ?StatusWave $status = null;

    /**
     * @var Collection<int, Order>
     */
    #[ORM\OneToMany(targetEntity: Order::class, mappedBy: "wave", orphanRemoval: true)]
    private Collection $orders;

    public function __construct()
    {
        $this->initializeUlid();
        $this->orders = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->start->format("Y/m/d - H:i");
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

    public function getStart(): ?\DateTimeInterface
    {
        return $this->start;
    }

    public function setStart(\DateTimeInterface $start): static
    {
        $this->start = $start;

        return $this;
    }

    public function getShop(): ?Shop
    {
        return $this->shop;
    }

    public function setShop(?Shop $shop): static
    {
        $this->shop = $shop;

        return $this;
    }

    public function getStatus(): ?StatusWave
    {
        return $this->status;
    }

    public function setStatus(?StatusWave $status): static
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Collection<int, Order>
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function addOrder(Order $order): static
    {
        if (!$this->orders->contains($order)) {
            $this->orders->add($order);
            $order->setWave($this);
        }

        return $this;
    }

    public function removeOrder(Order $order): static
    {
        if ($this->orders->removeElement($order)) {
            // set the owning side to null (unless already changed)
            if ($order->getWave() === $this) {
                $order->setWave(null);
            }
        }

        return $this;
    }
}
