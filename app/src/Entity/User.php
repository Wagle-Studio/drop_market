<?php

namespace App\Entity;

use App\Entity\Traits\Timestampable;
use App\Entity\Traits\Ulidable;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: "`user`")]
#[ORM\UniqueConstraint(name: "UNIQ_IDENTIFIER_EMAIL", fields: ["email"])]
#[UniqueEntity(fields: ["email"], message: "Il existe déjà un compte avec cette adresse email.")]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    use Timestampable;
    use Ulidable;

    public const ROLE_SUPER_ADMIN = "ROLE_SUPER_ADMIN";
    public const ROLE_ADMIN = "ROLE_ADMIN";
    public const ROLE_OWNER = "ROLE_OWNER";
    public const ROLE_EMPLOYEE = "ROLE_EMPLOYEE";
    public const ROLE_USER = "ROLE_USER";

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank(message: "Champs email requis.")]
    #[Assert\Length(
        min: 2,
        max: 180,
        minMessage: "L'email doit comporter au moins {{ limit }} caractères.",
        maxMessage: "L'email ne peut pas dépasser {{ limit }} caractères.",
    )]
    #[ORM\Column(length: 180)]
    private ?string $email = null;

    /**
     * @var string[]
     */
    #[ORM\Column(type: "json")]
    private array $roles = [];

    #[Assert\Length(
        min: 7,
        minMessage: "Le mot de passe doit comporter au moins {{ limit }} caractères.",
    )]
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column]
    private bool $isVerified = false;

    #[Assert\Length(
        min: 3,
        max: 50,
        minMessage: "Le nom doit comporter au moins {{ limit }} caractères.",
        maxMessage: "Le nom ne peut pas dépasser {{ limit }} caractères.",
    )]
    #[ORM\Column(length: 50)]
    private ?string $lastname = null;

    #[Assert\Length(
        min: 3,
        max: 50,
        minMessage: "Le prénom doit comporter au moins {{ limit }} caractères.",
        maxMessage: "Le prénom ne peut pas dépasser {{ limit }} caractères.",
    )]
    #[ORM\Column(length: 50)]
    private ?string $firstname = null;

    #[Vich\UploadableField(mapping: "user_avatars", fileNameProperty: "avatar")]
    private ?File $avatarFile = null;

    #[ORM\Column(nullable: true)]
    private ?string $avatar = null;

    /**
     * @var Collection<int, UserShop>
     */
    #[ORM\OneToMany(targetEntity: UserShop::class, mappedBy: "user", orphanRemoval: true)]
    private Collection $shops;

    /**
     * @var Collection<int, Order>
     */
    #[ORM\OneToMany(targetEntity: Order::class, mappedBy: "user", orphanRemoval: true)]
    private Collection $orders;

    public function __construct()
    {
        $this->initializeUlid();
        $hash = md5(strtolower(trim(strval(rand()))));
        $this->avatar = "https://www.gravatar.com/avatar/$hash?d=identicon&s=200";
        $this->shops = new ArrayCollection();
        $this->orders = new ArrayCollection();
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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = "ROLE_USER";

        return array_unique($roles);
    }

    /**
     * @param string[] $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here.
        // $this->plainPassword = null;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function getLastname(): string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): static
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getFirstname(): string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function setAvatarFile(?File $avatarFile = null): void
    {
        $this->avatarFile = $avatarFile;

        if (null !== $avatarFile) {
            $this->updated = new DateTime();
        }
    }

    public function getAvatarFile(): ?File
    {
        return $this->avatarFile;
    }

    public function setAvatar(?string $avatar): void
    {
        $this->avatar = $avatar;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    /**
     * @return array<int, Shop>
     */
    public function getShops(): array
    {
        if (!empty($this->shops)) {
            return array_map(function (UserShop $userShop) {
                return $userShop->getShop();
            }, $this->shops->toArray());
        }

        return [];
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
            $order->setUser($this);
        }

        return $this;
    }

    public function removeOrder(Order $order): static
    {
        if ($this->orders->removeElement($order)) {
            // set the owning side to null (unless already changed)
            if ($order->getUser() === $this) {
                $order->setUser(null);
            }
        }

        return $this;
    }
}
