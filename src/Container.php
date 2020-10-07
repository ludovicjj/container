<?php


namespace App;


use Psr\Container\ContainerInterface;

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
            $this->instances[$id] = new $id();
        }
        return $this->instances[$id];
    }

    /**
     * @inheritDoc
     */
    public function has($id): bool
    {
        return isset($this->instances[$id]);
    }
}