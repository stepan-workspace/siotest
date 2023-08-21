<?php

namespace App\Service\Payment\Providers;

use App\Service\Payment\Loader\PaymentLoaderInitializerInterface;
use Exception;

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

    /**
     * @throws Exception
     */
    public function createProvider(): PaymentProviderInterface
    {
        $provider = (string)match(true) {
            class_exists($this->providerClass) => $this->providerClass,
            default => throw new Exception('Payment system provider not found')
        };

        return match(true) {
            in_array(PaymentProviderInterface::class, class_implements($provider)) => new $provider(),
            default => throw new Exception('Failed to initialize the payment system provider')
        };
    }
}