<?php


namespace App\Tests\Classes;


class Second
{
    /**
     * @var First $simple
     */
    private $simple;

    /**
     * @var string $prefix
     */
    private $prefix;

    public function __construct(
        First $simple,
        $prefix = 'many_'
    ) {
        $this->simple = $simple;
        $this->prefix = $prefix;
    }

}