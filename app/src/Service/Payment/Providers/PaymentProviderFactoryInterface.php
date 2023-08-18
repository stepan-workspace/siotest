<?php

namespace App\Service\Payment\Providers;

interface PaymentProviderFactoryInterface
{
    public function createProvider(): ?PaymentProviderInterface;

    public function setProviderClass(string $providerClass): static;
}