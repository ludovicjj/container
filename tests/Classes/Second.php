<?php


namespace App\Tests\Classes;


use App\Tests\Classes\Interfaces\FirstInterface;
use App\Tests\Classes\Interfaces\SecondInterface;

class Second implements SecondInterface
{
    public function __construct(FirstInterface $first, $prefix = 'second_')
    {

    }
}
