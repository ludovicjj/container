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
     * @var string|null $alias
     */
    private $alias;

    /**
     * @var Definition[]
     */
    private $dependencies;

    public function __construct(
        string $id,
        bool $share = true,
        ?string $alias = null,
        array $dependencies = []
    ) {
        $this->id = $id;
        $this->share = $share;
        $this->alias = $alias;
        $this->dependencies = $dependencies;
    }
}