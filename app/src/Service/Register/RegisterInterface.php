<?php

namespace App\Service\Register;

interface RegisterInterface
{
    public function getParameter(string $name);

    public function hasParameter(string $name): bool;

    public function setParameter(string $name, array|bool|string|int|float|\UnitEnum|null $value);
}