<?php

namespace App\Service\Calculator;

class PriceBuilder implements PriceBuilderInterface
{
    private int $productId;

    private string $taxNumber;

    private ?string $couponCode;

    public function __construct(
        private readonly CalculatorInterface $calculator
    )
    {
    }

    public function setProductId(int $productId): static
    {
        $this->productId = $productId;
        return $this;
    }

    public function setTaxNumber(string $taxNumber): static
    {
        $this->taxNumber = $taxNumber;
        return $this;
    }

    public function setCouponCode(string $couponCode): static
    {
        $this->couponCode = $couponCode;
        return $this;
    }

    public function getPrice(): float
    {
        return $this->calculator->calculate(
            $this->productId,
            $this->taxNumber,
            $this->couponCode
        );
    }
}