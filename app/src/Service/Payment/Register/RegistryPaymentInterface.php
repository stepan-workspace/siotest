<?php

namespace App\Service\Payment\Register;

interface RegistryPaymentInterface
{
    public function register(string $key, string $class): void;

    public function get(string $key): ?string;

    public function isAvailable(string $key): bool;
}