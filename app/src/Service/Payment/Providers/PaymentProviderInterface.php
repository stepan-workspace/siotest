<?php

namespace App\Service\Payment\Providers;

interface PaymentProviderInterface
{
    public function processPayment(float $amount): bool;

    public function getError(): string;
}