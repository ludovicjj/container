<?php

namespace App;

use Psr\Container\ContainerInterface as PsrContainerInterface;

interface ContainerInterface extends PsrContainerInterface
{
    /**
     * Create definition to class.
     * If class got no-scalar dependencies, create definition foreach dependencies.
     * This method use singleton pattern
     *
     * @param string $id
     * @return Definition
     */
    public function getDefinition(string $id): Definition;

    /**
     * Register definition
     *
     * @param string $id
     * @return $this
     */
    public function register(string $id): self;

    /**
     * Add alias for interface class
     *
     * @param string $id
     * @param string $class
     * @return $this
     */
    public function addAlias(string $id, string $class): self;

    /**
     * Add key and value to constructor parameters
     *
     * @param string $id
     * @param $value
     * @return $this
     */
    public function addParameter(string $id, $value): self;

    /**
     * Get value of parameter
     *
     * @param string $id
     * @return mixed
     */
    public function getParameter(string $id);

    /**
     * Get value of alias
     *
     * @param string $id
     * @return string
     */
    public function getAlias(string $id): string;
}