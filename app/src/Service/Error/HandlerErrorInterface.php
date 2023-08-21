<?php

namespace App\Service\Error;

interface HandlerErrorInterface
{
    public function serve(string $class, object $source): static;

    public function toArray(): array;
}