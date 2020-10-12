<?php


namespace App;


use Psr\Container\ContainerInterface;
use ReflectionClass;
use ReflectionParameter;

class Definition
{
    /**
     * @var string $id
     */
    private $id;

    /**
     * @var bool $shared
     */
    private $shared;

    /**
     * @var string[] $aliases
     */
    private $aliases;

    /**
     * @var Definition[]
     */
    private $dependencies;

    /**
     * @var ReflectionClass
     */
    private $reflectionClass;

    public function __construct(
        string $id,
        bool $shared = true,
        array $aliases = [],
        array $dependencies = []
    ) {
        $this->id = $id;
        $this->shared = $shared;
        $this->aliases = $aliases;
        $this->dependencies = $dependencies;
        $this->reflectionClass = new ReflectionClass($id);
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return bool
     */
    public function isShared(): bool
    {
        return $this->shared;
    }

    /**
     * @param bool $shared
     * @return $this
     */
    public function setShared(bool $shared): self
    {
        $this->shared = $shared;
        return $this;
    }

    /**
     * @return string[]
     */
    public function getAliases(): array
    {
        return $this->aliases;
    }

    /**
     * @return Definition[]
     */
    public function getDependencies(): array
    {
        return $this->dependencies;
    }

    public function newInstance(ContainerInterface $container): object
    {
        $reflectionClass = $this->reflectionClass;
        $constructor = $reflectionClass->getConstructor();

        if ($constructor === null) {
            return $reflectionClass->newInstance();
        }

        $arguments = array_map(
            function (ReflectionParameter $parameter) use ($container) {
                return ($parameter->getClass())
                    ? $container->get($parameter->getClass()->getName())
                    : $parameter->getName();
            },
            $constructor->getParameters()
        );

        return $reflectionClass->newInstanceArgs($arguments);
    }
}