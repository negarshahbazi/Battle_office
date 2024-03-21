<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $price = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Photo $photo = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;



    #[ORM\Column(length: 255)]
    private ?string $firstPrice = null;

   

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getPhoto(): ?Photo
    {
        return $this->photo;
    }

    public function setPhoto(?Photo $photo): static
    {
        $this->photo = $photo;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

   

    public function getFirstPrice(): ?string
    {
        return $this->firstPrice;
    }

    public function setFirstPrice(string $firstPrice): static
    {
        $this->firstPrice = $firstPrice;

        return $this;
    }
    public function calculateDiscount(): int
    {
        return $this->firstPrice - $this->price;
    }
}
