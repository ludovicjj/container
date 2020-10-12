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
     *
     * @throws ReflectionException
     */
    public function getDefinition(string $id)
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
            // Add interface to definitions
            $this->definitions[$id] = &$this->definitions[$this->aliases[$id]];

            return $this;
        }

        if ($reflectionClass->getConstructor() !== null) {
            $dependencies = array_map(function(ReflectionParameter $parameter) {
                return ($parameter->getClass())
                    ? $this->getDefinition($parameter->getClass()->getName())
                    : $parameter->getName();
            }, $reflectionClass->getConstructor()->getParameters());
        }

        $aliases = array_filter($this->aliases, function($alias) use ($id) {
            return $alias === $id;
        }, 0);

        $definition = new Definition($id, true, $aliases, $dependencies);
        $this->definitions[$id] = $definition;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function get($id)
    {
        if (!$this->has($id)) {
            $instance = $this->resolve($id);

            if (!$this->getDefinition($id)->isShared()) {
                return $instance;
            }

            $this->instances[$id] = $instance;
        }

        return $this->instances[$id];
    }

    /**
     * @param string $id
     * @return Object
     * @throws ReflectionException
     */
    private function resolve(string $id): Object
    {
        $definition = $this->getDefinition($id);

        return $definition->newInstance($this);
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

    /**
     * @inheritDoc
     */
    public function has($id): bool
    {
        return isset($this->instances[$id]);
    }
}
