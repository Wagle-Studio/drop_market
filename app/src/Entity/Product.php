<?php

namespace App\Entity;

use App\Entity\Traits\SluggableTitle;
use App\Entity\Traits\Timestampable;
use App\Repository\ProductRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    use SluggableTitle;
    use Timestampable;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank(message: "Champs titre requis.")]
    #[Assert\Length(
        min: 2,
        max: 100,
        minMessage: "Le titre doit comporter au moins {{ limit }} caractères.",
        maxMessage: "Le titre ne peut pas dépasser {{ limit }} caractères.",
    )]
    #[ORM\Column(length: 100)]
    private ?string $title = null;

    #[Assert\Length(
        min: 10,
        max: 255,
        minMessage: "La description doit comporter au moins {{ limit }} caractères.",
        maxMessage: "La description ne peut pas dépasser {{ limit }} caractères.",
    )]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    #[Assert\NotBlank(message: "Champs prix requis.")]
    #[Assert\Type(
        type: "numeric",
        message: "Le prix doit être une valeur numérique."
    )]
    #[Assert\PositiveOrZero(message: "Le prix doit être supérieur ou égal à zéro.")]
    #[ORM\Column(type: "decimal", precision: 10, scale: 2)]
    private ?string $price_ttc = null;

    #[ORM\ManyToOne(inversedBy: 'products')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Shop $shop = null;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getPriceTtc(): ?string
    {
        return $this->price_ttc;
    }

    public function setPriceTtc(string $price_ttc): static
    {
        $this->price_ttc = $price_ttc;

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
}
