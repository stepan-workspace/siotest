<?php

namespace App\Service\Error;

use Symfony\Component\Form\FormInterface;

interface ToArrayErrorInterface
{
    public function setObject($object): static;

    public function toArray(): array;
}