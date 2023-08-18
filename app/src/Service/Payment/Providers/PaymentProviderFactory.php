<?php

namespace App\Service\Payment\Providers;

use App\Service\Payment\Loader\PaymentLoaderInitializerInterface;

class PaymentProviderFactory implements PaymentProviderFactoryInterface
{
    private string $providerClass;

    public function __construct(
        private readonly PaymentLoaderInitializerInterface $paymentLoader
    )
    {
        $this->paymentLoader->initialize();
    }

    public function setProviderClass(string $providerClass): static
    {
        $this->providerClass = $providerClass;
        return $this;
    }

    public function createProvider(): ?PaymentProviderInterface
    {
        $provider = $this->providerClass;
        if (class_exists($provider) && in_array(PaymentProviderInterface::class, class_implements($provider))) {
            return new $this->providerClass();
        }
        return null;
    }
}