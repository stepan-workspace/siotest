<?php

namespace App\Service\Payment\Providers;

use Exception;

class PaypalPaymentProvider implements PaymentProviderInterface
{
    private string $error;

    /**
     * @throws Exception
     */
    public function processPayment(float $amount): bool
    {
        try {
            $paypal = new \PaypalPaymentProcessor();
            $paypal->pay((int)$amount);
            return true;
        } catch (Exception $e) {
            $this->error = $e->getMessage();
            return false;
        }
    }

    public function getError(): string
    {
        return $this->error;
    }
}