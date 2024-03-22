<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: '`order`')]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;



    #[ORM\ManyToOne(inversedBy: 'orders')]
    private ?PaymentMethod $paymentMethod = null;

    #[ORM\ManyToOne(inversedBy: 'orders')]
    private ?Client $client = null;

 

    #[ORM\Column(length: 255)]
    private ?string $status = null;

    #[ORM\ManyToOne(inversedBy: 'orders')]
    private ?Product $product = null;

   

 

    public function __construct()
    {
        // $this->product = new ArrayCollection();
        $this->status = "WAITING";
    }




    public function getId(): ?int
    {
        return $this->id;
    }

 

    public function getPaymentMethod(): ?PaymentMethod
    {
        return $this->paymentMethod;
    }

    public function setPaymentMethod(?PaymentMethod $paymentMethod): static
    {
        $this->paymentMethod = $paymentMethod;

        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): static
    {
        $this->client = $client;

        return $this;
    }

 

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): static
    {
        $this->product = $product;

        return $this;
    }

 

   

  

}
