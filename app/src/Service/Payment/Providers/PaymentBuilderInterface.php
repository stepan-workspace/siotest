<?php

namespace App\Service\Payment\Providers;

interface PaymentBuilderInterface
{
    public function setPaymentProviderFactory(PaymentProviderFactoryInterface $factory): static;

    public function setPaymentKey(string $key): static;

    public function getPaymentProviderFactory(): PaymentProviderFactoryInterface;

    public function createProvider(): ?PaymentProviderInterface;
}