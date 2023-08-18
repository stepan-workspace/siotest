<?php

namespace App\Service\Payment\Providers;

use App\Service\Payment\Register\RegistryPaymentInterface;

class PaymentBuilder implements PaymentBuilderInterface
{
    public function __construct(
        private readonly array $payments,
        private readonly RegistryPaymentInterface $registry,
        private PaymentProviderFactoryInterface $factory,
        private string $paymentKey = ''
    )
    {
        $this->initialize();
    }

    public function setPaymentProviderFactory(PaymentProviderFactoryInterface $factory): static
    {
        $this->factory = $factory;
        return $this;
    }

    public function setPaymentKey(string $key): static
    {
        $this->paymentKey = $key;
        return $this;
    }

    public function getPaymentProviderFactory(): PaymentProviderFactoryInterface
    {
        return $this->factory;
    }

    public function createProvider(): ?PaymentProviderInterface
    {
        try {
            return $this->factory
                ->setProviderClass(
                    $this->registry->get(
                        $this->paymentKey
                    )
                )
                ->createProvider();
        } catch (\Exception $e) {
            dd($e->getMessage());
        }

    }

    private function initialize(): void
    {
        foreach ($this->payments as $k => $v) {
            $this->registry->register($k, $v::class);
        }
    }
}