<?php

namespace App\Service\Calculator;

interface CalculatorInterface
{
    public function calculate(float $price, float $discount, float $tax): float;
}