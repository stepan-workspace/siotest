<?php

namespace App\Entity;

use App\Repository\CountryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CountryRepository::class)]
#[ORM\UniqueConstraint(name: 'unique_code', columns: ['code'])]
#[UniqueEntity(fields: ['code'], message: 'This code is already in database.', errorPath: 'code')]
class Country implements \JsonSerializable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Assert\NotBlank(message: 'Country Name cannot be blank')]
    #[Assert\Length(min: 3, max: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 2)]
    #[Assert\NotBlank(message: 'Country Code cannot be blank')]
    #[Assert\Length(min: 1, max: 2)]
    private ?string $code = null;

    #[ORM\OneToMany(mappedBy: 'country', targetEntity: CountryTax::class)]
    private Collection $countryTaxes;

    public function __construct()
    {
        $this->countryTaxes = new ArrayCollection();
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

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @return Collection<int, CountryTax>
     */
    public function getCountryTaxes(): Collection
    {
        return $this->countryTaxes;
    }

    public function addCountryTax(CountryTax $countryTax): static
    {
        if (!$this->countryTaxes->contains($countryTax)) {
            $this->countryTaxes->add($countryTax);
            $countryTax->setCountry($this);
        }

        return $this;
    }

    public function removeCountryTax(CountryTax $countryTax): static
    {
        if ($this->countryTaxes->removeElement($countryTax)) {
            // set the owning side to null (unless already changed)
            if ($countryTax->getCountry() === $this) {
                $countryTax->setCountry(null);
            }
        }

        return $this;
    }

    public function jsonSerialize(): iterable
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'code' => $this->getCode()
        ];
    }
}
