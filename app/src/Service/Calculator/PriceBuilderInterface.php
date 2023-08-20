<?php

namespace App\Service\Calculator;

interface PriceBuilderInterface
{
    public function setCalculator(CalculatorInterface $calculator): static;

    public function setPrice(float $price): static;

    public function setDiscount(float $discount): static;

    public function setTax(float $tax): static;

    public function build(): float;
}