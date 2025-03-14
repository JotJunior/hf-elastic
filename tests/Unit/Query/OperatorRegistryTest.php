<?php

declare(strict_types=1);

namespace Jot\HfElastic\Tests\Unit\Query;

use Jot\HfElastic\Contracts\OperatorStrategyInterface;
use Jot\HfElastic\Query\OperatorRegistry;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Jot\HfElastic\Query\OperatorRegistry
 * @group unit
 */
class OperatorRegistryTest extends TestCase
{
    private OperatorRegistry $registry;

    protected function setUp(): void
    {
        $this->registry = new OperatorRegistry();
    }

    public function testRegisterAndFindStrategy(): void
    {
        // Arrange
        /** @var OperatorStrategyInterface|MockObject $operator */
        $operator = $this->createMock(OperatorStrategyInterface::class);
        $operator->expects($this->once())
            ->method('supports')
            ->with('=')
            ->willReturn(true);

        // Act
        $this->registry->register($operator);
        $result = $this->registry->findStrategy('=');

        // Assert
        $this->assertSame($operator, $result, 'Registry should return the registered operator');
    }

    public function testFindStrategyReturnsNullForUnregisteredOperator(): void
    {
        // Act
        $result = $this->registry->findStrategy('unknown_operator');

        // Assert
        $this->assertNull($result, 'Registry should return null for unregistered operators');
    }

    public function testRegisterMultipleOperators(): void
    {
        // Arrange
        /** @var OperatorStrategyInterface|MockObject $equalsOperator */
        $equalsOperator = $this->createMock(OperatorStrategyInterface::class);
        $equalsOperator->method('supports')
            ->willReturnCallback(function($op) {
                return $op === '=';
            });

        /** @var OperatorStrategyInterface|MockObject $notEqualsOperator */
        $notEqualsOperator = $this->createMock(OperatorStrategyInterface::class);
        $notEqualsOperator->method('supports')
            ->willReturnCallback(function($op) {
                return $op === '!=';
            });

        /** @var OperatorStrategyInterface|MockObject $greaterThanOperator */
        $greaterThanOperator = $this->createMock(OperatorStrategyInterface::class);
        $greaterThanOperator->method('supports')
            ->willReturnCallback(function($op) {
                return $op === '>';
            });

        // Act
        $this->registry->register($equalsOperator);
        $this->registry->register($notEqualsOperator);
        $this->registry->register($greaterThanOperator);

        // Assert
        $this->assertSame($equalsOperator, $this->registry->findStrategy('='), 'Registry should return the equals operator');
        $this->assertSame($notEqualsOperator, $this->registry->findStrategy('!='), 'Registry should return the not equals operator');
        $this->assertSame($greaterThanOperator, $this->registry->findStrategy('>'), 'Registry should return the greater than operator');
    }

    public function testRegisterOrderDeterminesPriority(): void
    {
        // Arrange
        /** @var OperatorStrategyInterface|MockObject $firstOperator */
        $firstOperator = $this->createMock(OperatorStrategyInterface::class);
        $firstOperator->method('supports')
            ->willReturn(true);

        /** @var OperatorStrategyInterface|MockObject $secondOperator */
        $secondOperator = $this->createMock(OperatorStrategyInterface::class);
        $secondOperator->method('supports')
            ->willReturn(true);

        // Act
        $this->registry->register($firstOperator);
        $this->registry->register($secondOperator);

        // Assert
        // The registry should return the first operator that supports the operator
        $this->assertSame($firstOperator, $this->registry->findStrategy('='), 'Registry should return the first operator that supports the operator');
    }
}
