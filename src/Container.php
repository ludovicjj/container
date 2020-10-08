<?php


namespace App;


use Psr\Container\ContainerInterface;
use ReflectionClass;
use ReflectionException;
use ReflectionParameter;

class Container implements ContainerInterface
{
    /**
     * @var array $instances
     */
    private $instances = [];

    /**
     * @var array $aliases
     */
    private $aliases = [];

    /**
     * @inheritDoc
     */
    public function get($id)
    {
        try {
            if (!$this->has($id)) {
                $reflectionClass = new ReflectionClass($id);

                if ($reflectionClass->isInterface()) {
                    return $this->resolveInterface($reflectionClass);
                }

                if ($this->hasConstructor($reflectionClass)) {
                    $this->resolveConstructor($reflectionClass);
                } else {
                    $this->resolve($reflectionClass);
                }
            }

            return $this->instances[$id];

        } catch (ReflectionException $exception) {
            throw new NotFoundException($exception->getMessage());
        }
    }

    /**
     * @inheritDoc
     */
    public function has($id): bool
    {
        return isset($this->instances[$id]);
    }


    /**
     * @param ReflectionClass $reflectionClass
     * @return bool
     */
    private function hasConstructor(ReflectionClass $reflectionClass): bool
    {
        return $reflectionClass->hasMethod('__construct');
    }

    /**
     * @param ReflectionClass $reflectionClass
     */
    private function resolve(ReflectionClass $reflectionClass): void
    {
        $id = $reflectionClass->getName();
        $this->instances[$id] =  $reflectionClass->newInstance();
    }

    /**
     * @param ReflectionClass $reflectionClass
     */
    private function resolveConstructor(ReflectionClass $reflectionClass)
    {
        $id = $reflectionClass->getName();
        $constructor = $reflectionClass->getConstructor();
        $parameters = $this->resolveConstructorParameters($constructor->getParameters());
        $this->instances[$id] = $reflectionClass->newInstanceArgs($parameters);
    }

    /**
     * @param ReflectionParameter[] $parameters
     * @return array
     */
    private function resolveConstructorParameters(array $parameters): array
    {
        return array_map(
            function (ReflectionParameter $parameter) {
                if ($parameter->getClass()) {
                    return $this->get($parameter->getClass()->getName());
                } else {
                    return $parameter->getName();
                }
            },
            $parameters
        );
    }

    /**
     * @param ReflectionClass $reflectionClass
     * @return Object
     * @throws NotFoundException
     */
    private function resolveInterface(ReflectionClass $reflectionClass)
    {
        $id = $reflectionClass->getName();

        if (isset($this->aliases[$id])) {
            return $this->get($this->aliases[$id]);
        } else {
            throw new NotFoundException(sprintf('Not found class %s', $reflectionClass->getName()));
        }
    }


    /**
     * @param string $id
     * @param string $class
     * @return $this
     */
    public function addAlias(string $id, string $class): self
    {
        $this->aliases[$id] = $class;
        return $this;
    }
}