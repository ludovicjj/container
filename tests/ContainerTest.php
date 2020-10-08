<?php


namespace App\Tests;


use App\Container;
use App\NotFoundException;
use App\Tests\Classes\Interfaces\FirstInterface;
use App\Tests\Classes\Second;
use App\Tests\Classes\First;
use App\Tests\Classes\Three;
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

    public function testReflectionException(): void
    {
        $unknownClass = 'App\Tests\Unknown';

        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage(sprintf('Class %s does not exist', $unknownClass));
        $this->container->get($unknownClass);
    }

    public function testResolve(): void
    {
        $this->assertInstanceOf(First::class, $this->container->get(First::class));
    }

    public function testResolveConstructor(): void
    {
        $this->assertInstanceOf(Second::class, $this->container->get(Second::class));
    }

    public function testResolveManyConstructor(): void
    {
        $this->assertInstanceOf(Three::class, $this->container->get(Three::class));
    }

    public function testSingleton(): void
    {
        $first1 = $this->container->get(First::class);
        $first2 = $this->container->get(First::class);
        $this->assertEquals(spl_object_id($first1), spl_object_id($first2));


        $second1 = $this->container->get(Second::class);
        $second2 = $this->container->get(Second::class);
        $this->assertEquals(spl_object_id($second1), spl_object_id($second2));
    }

    public function testResolveInterface(): void
    {
        $this->container->addAlias(FirstInterface::class, First::class);
        $this->assertInstanceOf(First::class, $this->container->get(FirstInterface::class));
    }

    public function testResolveInterfaceException(): void
    {
        $this->expectException(NotFoundException::class);
        $this->assertInstanceOf(First::class, $this->container->get(FirstInterface::class));
    }
}