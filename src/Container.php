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
     * @var string[] $aliases
     */
    private $aliases = [];

    /**
     * @var Definition[]
     */
    private $definitions = [];

    /**
     * @param string $id
     * @return Definition
     * @throws ReflectionException
     */
    public function getDefinition(string $id): Definition
    {
        if (!isset($this->definitions[$id])) {
            $this->register($id);
        }

        return $this->definitions[$id];
    }

    /**
     * @param string $id
     * @return $this
     * @throws ReflectionException
     */
    private function register(string $id): self
    {
        $reflectionClass = new ReflectionClass($id);
        $dependencies = [];

        if ($reflectionClass->isInterface()) {
            $this->register($this->aliases[$id]);
            $this->definitions[$id] = $this->definitions[$this->aliases[$id]];

            return $this;
        }

        if ($this->hasConstructor($reflectionClass)) {
            $dependencies = array_map(function(ReflectionParameter $parameter) {
                if ($parameter->getClass()) {
                    return $this->getDefinition($parameter->getClass()->getName());
                } else {
                    return $parameter->getName();
                }
            }, $reflectionClass->getConstructor()->getParameters());
        }

        $aliases = array_keys($this->aliases, $id);

        $definition = new Definition($id, true, $aliases, $dependencies);
        $this->definitions[$id] = $definition;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function get($id)
    {
        try {
            // singleton
            if (!$this->has($id)) {
                $reflectionClass = new ReflectionClass($id);

                // resolve interface service
                if ($reflectionClass->isInterface()) {
                    return $this->resolveInterface($reflectionClass);
                }

                // register service as definition with dependencies
                $this->register($id);

                // resolve constructor service
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
    private function resolveConstructor(ReflectionClass $reflectionClass): void
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