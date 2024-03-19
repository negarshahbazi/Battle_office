<?php

namespace App\Entity;

use App\Repository\CountryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CountryRepository::class)]
class Country
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\OneToMany(targetEntity: AddressBiling::class, mappedBy: 'country')]
    private Collection $addressBilings;

    #[ORM\OneToMany(targetEntity: AddressShipping::class, mappedBy: 'country')]
    private Collection $addressShippings;

    public function __construct()
    {
        $this->addressBilings = new ArrayCollection();
        $this->addressShippings = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * @return Collection<int, AddressBiling>
     */
    public function getAddressBilings(): Collection
    {
        return $this->addressBilings;
    }

    public function addAddressBiling(AddressBiling $addressBiling): static
    {
        if (!$this->addressBilings->contains($addressBiling)) {
            $this->addressBilings->add($addressBiling);
            $addressBiling->setCountry($this);
        }

        return $this;
    }

    public function removeAddressBiling(AddressBiling $addressBiling): static
    {
        if ($this->addressBilings->removeElement($addressBiling)) {
            // set the owning side to null (unless already changed)
            if ($addressBiling->getCountry() === $this) {
                $addressBiling->setCountry(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, AddressShipping>
     */
    public function getAddressShippings(): Collection
    {
        return $this->addressShippings;
    }

    public function addAddressShipping(AddressShipping $addressShipping): static
    {
        if (!$this->addressShippings->contains($addressShipping)) {
            $this->addressShippings->add($addressShipping);
            $addressShipping->setCountry($this);
        }

        return $this;
    }

    public function removeAddressShipping(AddressShipping $addressShipping): static
    {
        if ($this->addressShippings->removeElement($addressShipping)) {
            // set the owning side to null (unless already changed)
            if ($addressShipping->getCountry() === $this) {
                $addressShipping->setCountry(null);
            }
        }

        return $this;
    }
}
