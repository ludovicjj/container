<?php

namespace App;

use ReflectionClass;
use ReflectionException;
use ReflectionParameter;

class Container implements ContainerInterface
{
    /**
     * @var array
     */
    private $parameters = [];

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
    public function register(string $id): ContainerInterface
    {
        $reflectionClass = new ReflectionClass($id);
        $dependencies = [];

        if ($reflectionClass->isInterface()) {
            $this->register($this->aliases[$id]);
            // Add interface to definitions
            $this->definitions[$id] = &$this->definitions[$this->aliases[$id]];

            return $this;
        }

        if ($reflectionClass->getConstructor() !== null) {
            $parameters = $reflectionClass->getConstructor()->getParameters();

            // filter no scalar parameters
            $parameters = array_filter($parameters, function (ReflectionParameter $parameter) {
                return $parameter->getClass();
            });

            // Get dependencies to no scalar parameters
            $dependencies = array_map(function (ReflectionParameter $parameter) {
                return $this->getDefinition($parameter->getClass()->getName());
            }, $parameters);
        }

        $aliases = array_filter($this->aliases, function ($alias) use ($id) {
            return $alias === $id;
        }, 0);

        $this->definitions[$id] = new Definition($id, true, $aliases, $dependencies);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function get($id)
    {
        if (!$this->has($id)) {
            $instance = $this->getDefinition($id)->newInstance($this);

            if (!$this->getDefinition($id)->isShared()) {
                return $instance;
            }

            $this->instances[$id] = $instance;
        }

        return $this->instances[$id];
    }

    /**
     * @param string $id
     * @param string $class
     * @return $this
     */
    public function addAlias(string $id, string $class): ContainerInterface
    {
        $this->aliases[$id] = $class;

        return $this;
    }

    /**
     * @param string $id
     * @param mixed $value
     * @return $this
     */
    public function addParameter(string $id, $value): ContainerInterface
    {
        $this->parameters[$id] = $value;
        return $this;
    }

    /**
     * @param string $id
     * @return mixed
     */
    public function getParameter(string $id)
    {
        return $this->parameters[$id];
    }

    /**
     * @inheritDoc
     */
    public function has($id): bool
    {
        return isset($this->instances[$id]);
    }
}
