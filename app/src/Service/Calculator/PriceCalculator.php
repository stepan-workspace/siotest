<?php

namespace App\Service\Calculator;

class PriceCalculator implements CalculatorInterface
{
    public function calculate(float $price, float $discount, float $tax): float
    {
        $result = $price - $discount;
        $result += $result * ($tax / 100);
        return ceil($result * 100) / 100;
    }
}