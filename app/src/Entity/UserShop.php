<?php

namespace App\Entity;

use App\Entity\Traits\Timestampable;
use App\Repository\UserShopRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserShopRepository::class)]
class UserShop
{
    use Timestampable;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank(message: "Champs utilisateur requis.")]
    #[ORM\ManyToOne(inversedBy: "shops", fetch: "EAGER")]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[Assert\NotBlank(message: "Champs magasin requis.")]
    #[ORM\ManyToOne(inversedBy: "users", fetch: "EAGER")]
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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

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
