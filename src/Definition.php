<?php


namespace App;


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
}