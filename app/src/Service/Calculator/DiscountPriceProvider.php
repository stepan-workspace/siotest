<?php

namespace App\Service\Calculator;

use Exception;

class DiscountPriceProvider implements DiscountProviderInterface
{
    /**
     * @throws Exception
     */
    public function getDiscount(string $couponCode, float $price): float
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