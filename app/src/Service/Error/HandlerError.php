<?php

namespace App\Service\Error;

class HandlerError implements HandlerErrorInterface
{
    private array $result = [];

    public function serve(string $class, object $object): static
    {
        if (class_exists($class) && in_array(ToArrayErrorInterface::class, class_implements($class))) {
            $this->result = (new $class())->setObject($object)->toArray();
        }
        return $this;
    }

    public function toArray(): array
    {
        return $this->result;
    }
}