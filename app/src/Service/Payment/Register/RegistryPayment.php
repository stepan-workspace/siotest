<?php

namespace App\Service\Payment\Register;

class RegistryPayment implements RegistryPaymentInterface
{
    private array $registry = [];

    public function register(string $key, string $class): void
    {
        $this->registry[$key] = $class;
    }

    public function get(string $key): ?string
    {
        return $this->registry[$key] ?? '';
    }

    public function isAvailable(string $key): bool
    {
        return array_key_exists($key, $this->registry);
    }
}