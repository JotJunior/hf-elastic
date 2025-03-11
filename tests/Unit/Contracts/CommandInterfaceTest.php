<?php

declare(strict_types=1);

namespace Jot\HfElastic\Tests\Unit\Contracts;

use Jot\HfElastic\Contracts\CommandInterface;
use PHPUnit\Framework\TestCase;

/**
 * Testes para a interface CommandInterface
 */
class CommandInterfaceTest extends TestCase
{
    /**
     * @test
     * @group unit
     
     */
    public function testInterfaceExists(): void
    {
        // Assert that the interface exists
        $this->assertTrue(interface_exists(CommandInterface::class));
    }

    /**
     * @test
     * @group unit
     */
    public function testInterfaceHasRequiredMethods(): void
    {
        // Assert that the interface has the required methods
        $this->assertTrue(method_exists(CommandInterface::class, 'handle'));
    }

    /**
     * @test
     * @group unit
     
     */
    public function testInterfaceCanBeImplemented(): void
    {
        // Create a mock implementation of the interface
        $mock = $this->getMockBuilder(CommandInterface::class)
            ->getMock();

        // Assert that the mock is an instance of the interface
        $this->assertInstanceOf(CommandInterface::class, $mock);
    }
}
