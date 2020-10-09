<?php


namespace App\Tests;


use App\Container;
use App\Tests\Classes\Interfaces\FirstInterface;
use App\Tests\Classes\Interfaces\SecondInterface;
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

    public function testDefinition()
    {
        $this->container->addAlias(FirstInterface::class, First::class);
        $this->container->getDefinition(FirstInterface::class);

        $definition = $this->container->getDefinition(Second::class);
        $this->assertCount(2, $definition->getDependencies());

        $definition = $this->container
            ->addAlias(SecondInterface::class, Second::class)
            ->getDefinition(SecondInterface::class);

        $this->assertArrayHasKey(SecondInterface::class, $definition->getAliases());
    }
}