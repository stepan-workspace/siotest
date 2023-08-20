<?php

namespace App\Service\Payment;

class PaymentList implements PaymentListInterface
{
    public function __construct(
        private array $list = []
    )
    {
    }

    public function setList(array $list): static
    {
        $this->list = $list;
        return $this;
    }

    public function getList(): array
    {
        return $this->list;
    }
}