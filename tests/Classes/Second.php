<?php


namespace App\Tests\Classes;


use App\Tests\Classes\Interfaces\SecondInterface;

class Second implements SecondInterface
{
    /**
     * @var First $first
     */
    private $first;

    /**
     * @var string $prefix
     */
    private $prefix;

    public function __construct(
        First $first,
        $prefix = 'many_'
    ) {
        $this->first = $first;
        $this->prefix = $prefix;
    }

}