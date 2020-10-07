<?php


namespace App;


use Psr\Container\ContainerInterface;
use ReflectionClass;
use ReflectionParameter;

class Container implements ContainerInterface
{
    /**
     * @var array $instances
     */
    private $instances = [];

    /**
     * @inheritDoc
     */
    public function get($id)
    {
        if (!$this->has($id)) {

            $reflectionClass = new ReflectionClass($id);
            $constructor = $reflectionClass->getConstructor();

            if ($constructor === null) {
                $this->instances[$id] = $reflectionClass->newInstance();
            } else {
                $parameters = $this->resolveConstructorParameters($constructor->getParameters());

                $this->instances[$id] = $reflectionClass->newInstanceArgs($parameters);
            }
        }

        return $this->instances[$id];
    }

    public function getInstance(): array
    {
        return $this->instances;
    }

    /**
     * @inheritDoc
     */
    public function has($id): bool
    {
        return isset($this->instances[$id]);
    }

    /**
     * @param ReflectionParameter[] $parameters
     * @return Object[]
     */
    private function resolveConstructorParameters(array $parameters): array
    {
        return array_map( function (ReflectionParameter $parameter) {
            return $this->get($parameter->getClass()->getName());
        }, $parameters);
    }
}