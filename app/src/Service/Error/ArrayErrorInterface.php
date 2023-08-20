<?php

namespace App\Service\Error;

use Exception;

interface ArrayErrorInterface
{
    public function setSource($source): static;

    public function toArray(): array;
}