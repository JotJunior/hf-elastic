<?php

declare(strict_types=1);
/**
 * This file is part of hf-elastic
 *
 * @link     https://github.com/JotJunior/hf-elastic
 * @contact  hf-elastic@jot.com.br
 * @license  MIT
 */

namespace Jot\HfElastic\Tests\Unit\Contracts;

use Jot\HfElastic\Contracts\CommandInterface;
use PHPUnit\Framework\TestCase;

/**
 * @group unit
 * @internal
 * @coversNothing
 */
class CommandInterfaceTest extends TestCase
{
    /**
     * @test
     * @group unit
     * Test that the CommandInterface exists
     * What is being tested:
     * - The existence of the CommandInterface interface
     * Conditions/Scenarios:
     * - Checking if the interface class exists
     * Expected results:
     * - The interface should exist in the codebase
     */
    public function testInterfaceExists(): void
    {
        // Arrange & Act & Assert
        $this->assertTrue(interface_exists(CommandInterface::class), 'CommandInterface should exist');
    }

    /**
     * @test
     * @group unit
     * Test that the CommandInterface has the required methods
     * What is being tested:
     * - The presence of required methods in the CommandInterface
     * Conditions/Scenarios:
     * - Checking if the 'handle' method exists in the interface
     * Expected results:
     * - The interface should have a 'handle' method defined
     */
    public function testInterfaceHasRequiredMethods(): void
    {
        // Arrange & Act & Assert
        $this->assertTrue(method_exists(CommandInterface::class, 'handle'), "CommandInterface should have a 'handle' method");
    }

    /**
     * @test
     * @group unit
     * Test that the CommandInterface can be implemented
     * What is being tested:
     * - The ability to create a mock implementation of the interface
     * Conditions/Scenarios:
     * - Creating a mock object that implements the interface
     * Expected results:
     * - The mock should be an instance of CommandInterface
     */
    public function testInterfaceCanBeImplemented(): void
    {
        // Arrange
        $mock = $this->getMockBuilder(CommandInterface::class)
            ->getMock();

        // Act & Assert
        $this->assertInstanceOf(CommandInterface::class, $mock, 'Mock should be an instance of CommandInterface');
    }
}
