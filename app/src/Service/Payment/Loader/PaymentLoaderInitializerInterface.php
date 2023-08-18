<?php

namespace App\Service\Payment\Loader;

interface PaymentLoaderInitializerInterface
{
    public function initialize(): void;
}