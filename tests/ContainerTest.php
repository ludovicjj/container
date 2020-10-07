<?php


namespace App\Tests;


use App\Container;
use App\Tests\Fixtures\Database;
use App\Tests\Fixtures\Manager;
use PHPUnit\Framework\TestCase;

class ContainerTest extends TestCase
{
    /**
     * @var Container $container
     */
    private $container;

    public function setUp(): void
    {
        $this->container = new Container();
    }

    public function testInstance()
    {
        $database = $this->container->get(Database::class);
        $this->assertInstanceOf(Database::class, $database);
    }

    public function testObjectId()
    {
        $database1 = $this->container->get(Database::class);
        $database2 = $this->container->get(Database::class);

        $this->assertInstanceOf(Database::class, $database1);
        $this->assertInstanceOf(Database::class, $database2);
        $this->assertEquals(spl_object_id($database1), spl_object_id($database2));
    }

    public function testWithConstructor()
    {
        $manager = $this->container->get(Manager::class);
        $this->assertInstanceOf(Manager::class, $manager);
    }
}