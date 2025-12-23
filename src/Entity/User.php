<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity]
#[ORM\Table(name: '`user`')]
#[UniqueEntity(fields: ['email'], message: 'Il existe déjà un compte avec cet email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column('id', type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(name: 'lastname', length: 50, type: Types::STRING)]
    private ?string $lastname = null;

    #[ORM\Column(name: 'firstname', length: 50, type: Types::STRING)]
    private ?string $firstname = null;

    #[ORM\Column(name: 'email', length: 190, type: Types::STRING, unique: true)]
    private ?string $email = null;

    #[ORM\Column(name: 'password', length: 255, type: Types::STRING)]
    private ?string $password = null;

    #[ORM\Column(name: 'api_access', type: Types::BOOLEAN)]
    private ?bool $apiAccess = null;

    #[ORM\Column(name: 'created_at', type: Types::DATETIME_MUTABLE)]
    private ?\DateTime $createdAt = null;

    #[ORM\OneToMany(mappedBy: "user", targetEntity: Order::class, cascade: ["remove"])]
    private Collection $orders;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->apiAccess = false;
        $this->orders = new ArrayCollection();
    }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): static
    {
        $this->lastname = ucfirst($lastname);

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): static
    {
        $this->firstname = ucfirst($firstname);

        return $this;
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

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function isApiAccess(): ?bool
    {
        return $this->apiAccess;
    }

    public function setApiAccess(bool $apiAccess): static
    {
        $this->apiAccess = $apiAccess;

        return $this;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }
    public function getRoles(): array
    {
        return ['ROLE_USER'];
    }
    public function eraseCredentials(): void {}
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getOrders(): Collection
    {
        return $this->orders;
    }
    public function addOrder(Order $order): self
    {
        if (!$this->orders->contains($order)) {
            $this->orders->add($order);
            $order->setUser($this);
        }

        return $this;
    }
    public function removeOrder(Order $order): self
    {
        $this->orders->removeElement($order);

        return $this;
    }
}
