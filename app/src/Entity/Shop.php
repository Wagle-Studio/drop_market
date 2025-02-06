<?php

namespace App\Entity;

use App\Entity\Traits\SluggableTitle;
use App\Entity\Traits\Timestampable;
use App\Entity\Traits\Ulidable;
use App\Repository\ShopRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ShopRepository::class)]
class Shop
{
    use SluggableTitle;
    use Timestampable;
    use Ulidable;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank(message: "Champs titre requis.")]
    #[Assert\Length(
        min: 5,
        max: 100,
        minMessage: "Le titre doit comporter au moins {{ limit }} caractères.",
        maxMessage: "Le titre ne peut pas dépasser {{ limit }} caractères.",
    )]
    #[ORM\Column(length: 100)]
    private ?string $title = null;

    #[Assert\NotBlank(message: "Champs code postal requis.")]
    #[Assert\Length(
        min: 5,
        max: 10,
        minMessage: "Le code postal doit comporter au moins {{ limit }} caractères.",
        maxMessage: "Le code postal ne peut pas dépasser {{ limit }} caractères.",
    )]
    #[ORM\Column(length: 10)]
    private ?string $postalCode = null;

    /**
     * @var Collection<int, UserShop>
     */
    #[ORM\OneToMany(targetEntity: UserShop::class, mappedBy: "shop", orphanRemoval: true)]
    private Collection $users;

    /**
     * @var Collection<int, Wave>
     */
    #[ORM\OneToMany(targetEntity: Wave::class, mappedBy: 'shop', orphanRemoval: true)]
    private Collection $waves;

    /**
     * @var Collection<int, Product>
     */
    #[ORM\OneToMany(targetEntity: Product::class, mappedBy: 'shop', orphanRemoval: true)]
    private Collection $products;

    public function __construct()
    {
        $this->initializeUlid();
        $this->users = new ArrayCollection();
        $this->waves = new ArrayCollection();
        $this->products = new ArrayCollection();
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

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function setPostalCode(string $postalCode): static
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    /**
     * @return array<int, User>
     */
    public function getUsers(): array
    {
        if (!empty($this->users)) {
            return array_map(function (UserShop $userShop) {
                return $userShop->getUser();
            }, $this->users->toArray());
        }

        return [];
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
            $wave->setShop($this);
        }

        return $this;
    }

    public function removeWave(Wave $wave): static
    {
        if ($this->waves->removeElement($wave)) {
            // set the owning side to null (unless already changed)
            if ($wave->getShop() === $this) {
                $wave->setShop(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Product>
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(Product $product): static
    {
        if (!$this->products->contains($product)) {
            $this->products->add($product);
            $product->setShop($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): static
    {
        if ($this->products->removeElement($product)) {
            // set the owning side to null (unless already changed)
            if ($product->getShop() === $this) {
                $product->setShop(null);
            }
        }

        return $this;
    }
}
