<?php

namespace App\Service\Payment\Loader;

class PaymentLoader
{
    public function load($className): void
    {
        if (!class_exists($className) && file_exists($file = __DIR__ . '/../PaymentProcessor/' . $className . '.php')) {
            require_once $file;
        }
    }
}