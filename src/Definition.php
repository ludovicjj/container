<?php


namespace App;


class Definition
{
    /**
     * @var string $id
     */
    private $id;

    /**
     * @var bool $share
     */
    private $share;

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
        bool $share = true,
        array $aliases = [],
        array $dependencies = []
    ) {
        $this->id = $id;
        $this->share = $share;
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
    public function isShare(): bool
    {
        return $this->share;
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