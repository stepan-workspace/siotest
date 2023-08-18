<?php

namespace App\Service\Payment\Providers;

use Exception;

class StripePaymentProvider implements PaymentProviderInterface
{

    private string $error;

    public function processPayment(float $amount): bool
    {
        try {
            $stripe = new \StripePaymentProcessor();
            return $stripe->processPayment((int)$amount);
        } catch (Exception $e) {
            $this->error = $e->getMessage();
            return false;
        }
    }

    public function getError(): string
    {
        return $this->error ?? '';
    }
}