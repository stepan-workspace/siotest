<?php

namespace App\Service\Payment\Providers;

use Exception;

abstract class PaymentProviderAbstract
{
    protected string $errorMessage;

    public function processPayment(float $amount): bool
    {
        try {
            return $this->process($amount);
        } catch (Exception $e) {
            $this->errorMessage = $e->getMessage();
            return false;
        }
    }

    abstract public function process(float $amount): bool;

    public function getErrorMessage(): string
    {
        return $this->errorMessage ?? '';
    }
}