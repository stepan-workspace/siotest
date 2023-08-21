<?php

namespace App\Service\Register;

use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Register implements RegisterInterface
{
    private ContainerInterface $container;

    public function __construct()
    {
        $this->container = new Container();
    }

    public function getParameter(string $name): \UnitEnum|float|array|bool|int|string|null
    {
        return $this->container->getParameter($name);
    }

    public function hasParameter(string $name): bool
    {
        return $this->container->hasParameter($name);
    }

    public function setParameter(string $name, \UnitEnum|float|int|bool|array|string|null $value): void
    {
        $this->container->setParameter($name, $value);
    }
}