<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name:'id', type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(name:'name', length: 150, type: Types::STRING)]
    #[Assert\NotBlank(message: "Le nom du produit est obligatoire")]
    private ?string $name = null;

    #[ORM\Column(name:'short_description', length:255, type: Types::STRING)]
    #[Assert\NotBlank(message: "La courte description est obligatoire")]
    #[Assert\Length(
        max: 255,
        maxMessage: "La courte description ne peut pas dépasser {{ limit }} caractères"
    )]
    private ?string $shortDescription = null;

    #[ORM\Column(name:'full_description', type: Types::TEXT)]
    #[Assert\NotBlank(message: "La description complète est obligatoire")]
    private ?string $fullDescription = null;

    #[ORM\Column(name:'price', type: Types::FLOAT)]
    #[Assert\NotBlank(message: "Le prix est obligatoire")]
    #[Assert\Positive(message: "Le prix doit être positif")]
    private ?float $price = null;

    #[ORM\Column(name:'picture',length: 255, type: Types::STRING)]
    private ?string $picture = null;

    #[ORM\Column(name:'created_at', type: Types::DATETIME_MUTABLE)]
    private ?\DateTime $createdAt = null;
    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getShortDescription(): ?string
    {
        return $this->shortDescription;
    }

    public function setShortDescription(string $shortDescription): self
    {
        $this->shortDescription = $shortDescription;

        return $this;
    }

    public function getFullDescription(): ?string
    {
        return $this->fullDescription;
    }

    public function setFullDescription(string $fullDescription): self
    {
        $this->fullDescription = $fullDescription;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(string $picture): self
    {
        $this->picture = $picture;

        return $this;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
