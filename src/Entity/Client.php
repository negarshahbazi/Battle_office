<?php

namespace App\Entity;

use App\Repository\ClientRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ClientRepository::class)]
class Client
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $firstName = null;

    #[ORM\Column(length: 255)]
    private ?string $lastName = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?AddressBiling $addressBiling = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?AddressShipping $addressShipping = null;

    #[ORM\OneToMany(targetEntity: Order::class, mappedBy: 'client')]
    private Collection $orders;

    #[ORM\Column(length: 255)]
    private ?string $confirmationEmail = null;

    public function __construct()
    {
        $this->orders = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

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

    public function getAddressBiling(): ?AddressBiling
    {
        return $this->addressBiling;
    }

    public function setAddressBiling(?AddressBiling $addressBiling): static
    {
        $this->addressBiling = $addressBiling;

        return $this;
    }

    public function getAddressShipping(): ?AddressShipping
    {
        return $this->addressShipping;
    }

    public function setAddressShipping(?AddressShipping $addressShipping): static
    {
        $this->addressShipping = $addressShipping;

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
            $order->setClient($this);
        }

        return $this;
    }

    public function removeOrder(Order $order): static
    {
        if ($this->orders->removeElement($order)) {
            // set the owning side to null (unless already changed)
            if ($order->getClient() === $this) {
                $order->setClient(null);
            }
        }

        return $this;
    }

    public function getConfirmationEmail(): ?string
    {
        return $this->confirmationEmail;
    }

    public function setConfirmationEmail(string $confirmationEmail): static
    {
        $this->confirmationEmail = $confirmationEmail;

        return $this;
    }
}
