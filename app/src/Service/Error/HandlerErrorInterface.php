<?php

namespace App\Service\Error;

interface HandlerErrorInterface
{
    public function serve(string $class, object $object): static;

    public function toArray(): array;
}