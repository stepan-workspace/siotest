<?php

namespace App\Entity;

use App\Repository\CountryTaxRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CountryTaxRepository::class)]
class CountryTax
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'countryTaxes')]
    private ?Country $country = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "Tax Value cannot be blank")]
    private ?int $value = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Tax Rule cannot be blank")]
    #[Assert\Length(min: 3, max: 255)]
    private ?string $rule = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCountry(): ?Country
    {
        return $this->country;
    }

    public function setCountry(?Country $country): static
    {
        $this->country = $country;

        return $this;
    }

    public function getValue(): ?int
    {
        return $this->value;
    }

    public function setValue(int $value): static
    {
        $this->value = $value;

        return $this;
    }

    public function getRule(): ?string
    {
        return $this->rule;
    }

    public function setRule(string $rule): static
    {
        $this->rule = $rule;

        return $this;
    }
}
