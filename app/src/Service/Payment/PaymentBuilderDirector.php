<?php

namespace App\Service\Payment;

class PaymentBuilderDirector implements PaymentBuilderDirectorInterface
{
    public function __construct(
        private PaymentBuilderInterface       $builder,
        private readonly PaymentListInterface $paymentList,
    )
    {
    }

    public function setPaymentBuilder(PaymentBuilderInterface $builder): static
    {
        $this->builder = $builder;
        return $this;
    }

    public function buildComplete($paymentProcessor, $amount): bool
    {
        foreach ($this->paymentList->getList() as $k => $v) {
            $this->builder->addPaymentProvider($k, $v::class);
        }

        return $this->builder
            ->setPaymentProcessor($paymentProcessor)
            ->setAmount($amount)
            ->build();
    }
}