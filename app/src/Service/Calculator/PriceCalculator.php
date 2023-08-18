<?php

namespace App\Service\Calculator;

use App\Repository\CountryTaxRepository;
use App\Repository\ProductRepository;
use Exception;

class PriceCalculator implements CalculatorInterface
{
    public function __construct(
        private readonly ProductRepository $productRepository,
        private readonly CountryTaxRepository $countryTaxRepository
    )
    {
    }

    /**
     * @throws Exception
     */
    public function calculate(int $productId, string $taxNumber, string $couponCode): float
    {
        $price = $this->getPriceByProductId($productId);

        $discount = $this->getDiscountByCouponCode($couponCode, $price);

        $tax = $this->getTaxCountryByTaxNumber($taxNumber);

        $result = $price - $discount;

        $result += $result * ($tax / 100);

        return ceil($result * 100) / 100;
    }

    /**
     * @throws Exception
     */
    private function getPriceByProductId(int $productId): float
    {
        $price = (float)$this->productRepository->find($productId)?->getPrice();
        if (!$price) {
            throw new Exception('Product not fount by Id: ' . $productId);
        }
        return $price;
    }

    private function getTaxCountryByTaxNumber(string $taxNumber): int
    {
        if (!preg_match('/^(?P<code>[a-z]{2})/i', $taxNumber, $taxData)) {
            return 0;
        }

        $taxes = $this->countryTaxRepository->getTaxesByCountryCode($taxData['code']);

        foreach ($taxes as $tax) {
            if (preg_match($tax['rule'], $taxNumber)) {
                return $tax['value'];
            }
        }
        return 0;
    }

    /**
     * @throws Exception
     */
    private function getDiscountByCouponCode(string $couponCode, float $price): float
    {
        if (!$couponCode) {
            return 0;
        }

        return (float)match(true) {
            preg_match('/^D(?P<v>\d+)$/i', $couponCode, $data) > 0 => $data['v'],
            preg_match('/^P(?P<v>\d+)$/i', $couponCode, $data) > 0 => $price*($data['v']/100),
            default => throw new Exception('Unexpected match value by: ' . $couponCode)
        };
    }
}