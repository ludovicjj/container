<?php


namespace App\Tests\Classes;


class Database
{
    public function __construct(
        First $first,
        string $dbHost,
        string $dbName,
        string $dbUser,
        string $dbPassword,
        string $foo = 'foo'
    ) {

    }
}