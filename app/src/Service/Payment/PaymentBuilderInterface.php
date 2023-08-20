<?php

namespace App\Service\Payment;

interface PaymentBuilderInterface
{
    public function addPaymentProvider(string $name, string $value): static;

    public function setPaymentProcessor(string $paymentProcessor): static;

    public function setAmount(float $amount): static;

    public function build(): bool;
}