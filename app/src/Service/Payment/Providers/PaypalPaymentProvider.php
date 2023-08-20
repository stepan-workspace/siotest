<?php

namespace App\Service\Payment\Providers;

use Exception;

class PaypalPaymentProvider extends PaymentProviderAbstract implements PaymentProviderInterface
{
    /**
     * @throws Exception
     */
    public function process(float $amount): bool
    {
        (new \PaypalPaymentProcessor())->pay((int)$amount);
        return true;
    }
}