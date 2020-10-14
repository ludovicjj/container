<?php


namespace App\Tests;


use App\Container;
use App\Tests\Classes\Database;
use App\Tests\Classes\Interfaces\FirstInterface;
use App\Tests\Classes\Interfaces\SecondInterface;
use App\Tests\Classes\Second;
use App\Tests\Classes\First;
use App\Tests\Classes\Three;
use PHPUnit\Framework\TestCase;
use Psr\Container\NotFoundExceptionInterface;
use ReflectionException;

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

    public function testResolve(): void
    {
        $this->container
            ->addAlias(SecondInterface::class, Second::class)
            ->addAlias(FirstInterface::class, First::class)
        ;

        $this->assertInstanceOf(Second::class, $this->container->get(SecondInterface::class));

        $three1 = $this->container->get(Three::class);
        $three2 = $this->container->get(Three::class);

        $this->assertEquals(spl_object_id($three1), spl_object_id($three2));
    }

    /**
     * @throws ReflectionException
     */
    public function testDefinition(): void
    {
        $this->container
            ->addAlias(FirstInterface::class, First::class)
            ->addAlias(SecondInterface::class, Second::class)
        ;

        $definition = $this->container->getDefinition(Second::class);
        $this->assertEquals(Second::class, $definition->getId());
        $this->assertCount(1, $definition->getDependencies());

        $definition = $this->container->getDefinition(SecondInterface::class);
        $this->assertArrayHasKey(SecondInterface::class, $definition->getAliases());
    }

    public function testShared(): void
    {
        $this->container->addAlias(FirstInterface::class, First::class);

        $three1 = $this->container->get(Three::class);
        $three2 = $this->container->get(Three::class);

        $this->assertEquals(spl_object_id($three1), spl_object_id($three2));
    }

    /**
     * @throws ReflectionException
     */
    public function testNotShared(): void
    {
        $this->container->getDefinition(First::class)->setShared(false);

        $first1 = $this->container->get(First::class);
        $first2 = $this->container->get(First::class);

        $this->assertNotEquals(spl_object_id($first1), spl_object_id($first2));
    }

    public function testParameters(): void
    {
        $this->container
            ->addParameter('dbHost', "localhost")
            ->addParameter('dbName', "my_database")
            ->addParameter('dbUser', "root")
            ->addParameter('dbPassword', "")
        ;
        $database = $this->container->get(Database::class);
        $this->assertInstanceOf(Database::class, $database);
    }

    public function testClassNotFoundException()
    {
        $this->expectException(NotFoundExceptionInterface::class);
        $this->container->get(Bar::class);
    }

    public function testParameterNotFoundException()
    {
        $this->expectException(NotFoundExceptionInterface::class);
        $this->container->get(Database::class);
    }

    public function testAliasNotFoundException()
    {
        $this->expectException(NotFoundExceptionInterface::class);
        $this->container->get(FirstInterface::class);
    }
}