<?php

namespace App\Service\Calculator;

interface DiscountProviderInterface
{
    public function getDiscount(string $couponCode, float $price): float;
}