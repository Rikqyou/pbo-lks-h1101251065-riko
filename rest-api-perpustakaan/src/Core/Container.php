<?php

namespace App\Core;

use ReflectionClass;

class Container
{
    private array $bindings = [];

    public function bind(string $abstract, callable|string $concrete): void
    {
        $this->bindings[$abstract] = $concrete;
    }

    public function make(string $abstract)
    {
        if (isset($this->bindings[$abstract])) {
            $concrete = $this->bindings[$abstract];
            if (is_callable($concrete)) {
                return $concrete($this);
            }
            $abstract = $concrete;
        }

        $reflector = new ReflectionClass($abstract);
        $constructor = $reflector->getConstructor();

        if (null === $constructor) {
            return new $abstract();
        }

        $dependencies = [];
        foreach ($constructor->getParameters() as $param) {
            $type = $param->getType();
            if ($type && !$type->isBuiltin()) {
                $dependencies[] = $this->make($type->getName());
            }
        }

        return $reflector->newInstanceArgs($dependencies);
    }

    public function get(string $abstract)
    {
        return $this->make($abstract);
    }
}