<?php

namespace App\Service\Calculator;

class PriceBuilder implements PriceBuilderInterface
{
    public function __construct(
        private CalculatorInterface $calculator,
        private float               $price = 0,
        private float               $discount = 0,
        private float               $tax = 0
    )
    {
    }

    public function setCalculator(CalculatorInterface $calculator): static
    {
        $this->calculator = $calculator;
        return $this;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;
        return $this;
    }

    public function setDiscount(float $discount): static
    {
        $this->discount = $discount;
        return $this;
    }

    public function setTax(float $tax): static
    {
        $this->tax = $tax;
        return $this;
    }

    public function build(): float
    {
        return $this->calculator->calculate(
            $this->price,
            $this->discount,
            $this->tax,
        );
    }
}