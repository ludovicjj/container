<?php


namespace App\Tests;


use App\Container;
use App\Tests\Fixtures\Database;
use PHPUnit\Framework\TestCase;

class ContainerTest extends TestCase
{
    public function testInstance()
    {
        $container = new Container();

        $database1 = $container->get(Database::class);
        $database2 = $container->get(Database::class);

        $this->assertInstanceOf(Database::class, $database1);
        $this->assertInstanceOf(Database::class, $database2);
        $this->assertEquals(spl_object_id($database1), spl_object_id($database2));
    }
}