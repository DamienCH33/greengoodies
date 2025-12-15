<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
class User implements UserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column('id', type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(name: 'lastname', length: 50, type: Types::STRING)]
    #[Assert\NotBlank(message: "Le nom est obligatoire")]
    private ?string $lastname = null;

    #[ORM\Column(name:'fisrtname', length: 50, type: Types::STRING)]
    #[Assert\NotBlank(message: "Le prénom est obligatoire")]
    private ?string $fisrtname = null;

    #[ORM\Column(name:'email', length: 190, type: Types::STRING, unique: true)]
    #[Assert\NotBlank(message: "L'email est obligatoire")]
    #[Assert\Email(message: "L'adresse email n'est pas valide")]
    private ?string $email = null;

    #[ORM\Column(name:'password', length: 255, type: Types::STRING)]
    #[Assert\NotBlank(message: "Le mot de passe est obligatoire")]
    #[Assert\Length(min: 8, minMessage: "Le mot de passe doit faire au moins {{ limit }} caractères")]
    private ?string $password = null;

    #[ORM\Column(name:'api_acces', type: Types::BOOLEAN)]
    private ?bool $apiAcces = null;

    #[ORM\Column(name:'created_at', type: Types::DATETIME_MUTABLE)]

    #[ORM\OneToMany(mappedBy:"user", targetEntity:Order::class, cascade:["remove"])]
    private Collection $orders;
    private ?\DateTime $createdAt = null;
    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->apiAcces = false;
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
        $this->lastname = $lastname;

        return $this;
    }

    public function getFisrtname(): ?string
    {
        return $this->fisrtname;
    }

    public function setFisrtname(string $fisrtname): static
    {
        $this->fisrtname = $fisrtname;

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

    public function isApiAcces(): ?bool
    {
        return $this->apiAcces;
    }

    public function setApiAcces(bool $apiAcces): static
    {
        $this->apiAcces = $apiAcces;

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
     public function eraseCredentials(): void
    {
      
    }
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
