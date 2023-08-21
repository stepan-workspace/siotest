<?php

namespace App\Service\Payment\Providers;

use Exception;

class StripePaymentProvider extends PaymentProviderAbstract implements PaymentProviderInterface
{
    public function process(float $amount): bool
    {
        return (new \StripePaymentProcessor())->processPayment((int)$amount);
    }
}