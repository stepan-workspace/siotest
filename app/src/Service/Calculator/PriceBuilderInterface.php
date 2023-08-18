<?php

namespace App\Service\Calculator;

interface PriceBuilderInterface
{
    public function setProductId(int $productId): static;

    public function setTaxNumber(string $taxNumber): static;

    public function setCouponCode(string $couponCode): static;

    public function getPrice(): float;
}