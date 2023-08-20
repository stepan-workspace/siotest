<?php

namespace App\Service\Payment;

use App\Service\Payment\Providers\PaymentProviderFactoryInterface;
use App\Service\Register\RegisterInterface;
use Exception;

class PaymentBuilder implements PaymentBuilderInterface
{
    public function __construct(
        private readonly RegisterInterface               $register,
        private readonly PaymentProviderFactoryInterface $factory,
        private string                                   $paymentProcessor = '',
        private float                                    $amount = 0
    )
    {
    }

    public function addPaymentProvider(string $name, string $value): static
    {
        $this->register->setParameter($name, $value);
        return $this;
    }

    public function setPaymentProcessor(string $paymentProcessor): static
    {
        $this->paymentProcessor = $paymentProcessor;
        return $this;
    }

    public function setAmount(float $amount): static
    {
        $this->amount = $amount;
        return $this;
    }

    /**
     * @throws Exception
     */
    public function build(): bool
    {
        $providerClass = $this->register->getParameter($this->paymentProcessor);

        $provider = $this->factory
            ->setProviderClass($providerClass)
            ->createProvider();

        return match (true) {
            $provider->processPayment($this->amount) => true,
            default => throw new Exception($provider->getErrorMessage())
        };
    }
}