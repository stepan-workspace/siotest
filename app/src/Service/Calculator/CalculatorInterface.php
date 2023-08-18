<?php

namespace App\Service\Calculator;

interface CalculatorInterface
{
    public function calculate(int $productId, string $taxNumber, string $couponCode): float;
}