<?php

namespace App\Service\Error;

class HandlerError implements HandlerErrorInterface
{
    private ?ArrayErrorInterface $arrayError;

    public function serve(string $class, object $source): static
    {
        if (class_exists($class) && in_array(ArrayErrorInterface::class, class_implements($class))) {
            $this->arrayError = new $class($source);
        }
        return $this;
    }

    public function toArray(): array
    {
        return $this->arrayError->toArray();
    }
}