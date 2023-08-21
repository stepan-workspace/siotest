<?php

namespace App\Service\Payment;

interface PaymentListInterface
{
    public function setList(array $list): static;

    public function getList(): array;
}