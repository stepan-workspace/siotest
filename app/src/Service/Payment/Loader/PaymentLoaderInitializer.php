<?php

namespace App\Service\Payment\Loader;

class PaymentLoaderInitializer implements PaymentLoaderInitializerInterface
{
    private PaymentLoader $classLoader;

    public function __construct(PaymentLoader $classLoader)
    {
        $this->classLoader = $classLoader;
    }

    public function initialize(): void
    {
        spl_autoload_register([$this->classLoader, 'load']);
    }
}