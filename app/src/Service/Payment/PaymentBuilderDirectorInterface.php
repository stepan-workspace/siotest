<?php

namespace App\Service\Payment;

interface PaymentBuilderDirectorInterface
{
    public function setPaymentBuilder(PaymentBuilderInterface $builder): static;

    public function buildComplete($paymentProcessor, $amount): bool;
}