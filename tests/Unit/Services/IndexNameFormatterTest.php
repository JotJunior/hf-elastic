<?php

declare(strict_types=1);
/**
 * This file is part of hf-elastic
 *
 * @link     https://github.com/JotJunior/hf-elastic
 * @contact  hf-elastic@jot.com.br
 * @license  MIT
 */

namespace Jot\HfElastic\Tests\Unit\Services;

use Jot\HfElastic\Services\IndexNameFormatter;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
class IndexNameFormatterTest extends TestCase
{
    private IndexNameFormatter $formatter;

    protected function setUp(): void
    {
        $this->formatter = new IndexNameFormatter('app');
    }

    public function testFormatWithPrefix(): void
    {
        // Arrange
        $indexName = 'users';
        $expectedResult = 'app_users';

        // Act
        $result = $this->formatter->format($indexName);

        // Assert
        $this->assertEquals($expectedResult, $result, 'Formatter should add prefix to index name');
    }

    public function testFormatWithoutPrefix(): void
    {
        // Arrange
        $indexName = 'users';
        $expectedResult = 'users';

        // Create a new formatter with empty prefix
        $this->formatter = new IndexNameFormatter('');

        // Act
        $result = $this->formatter->format($indexName);

        // Assert
        $this->assertEquals($expectedResult, $result, 'Formatter should return index name unchanged when no prefix is set');
    }

    public function testFormatWithPrefixAlreadyApplied(): void
    {
        // Arrange
        $indexName = 'app_users';
        $expectedResult = 'app_users';

        // Act
        $result = $this->formatter->format($indexName);

        // Assert
        $this->assertEquals($expectedResult, $result, 'Formatter should not add prefix if it is already present');
    }

    public function testFormatMultipleIndices(): void
    {
        // Arrange
        $indices = ['users', 'products', 'orders'];
        $expectedResult = ['app_users', 'app_products', 'app_orders'];

        // Act
        $result = array_map([$this->formatter, 'format'], $indices);

        // Assert
        $this->assertEquals($expectedResult, $result, 'Formatter should add prefix to all index names');
    }

    public function testFormatWithEmptyIndexName(): void
    {
        // Arrange
        $indexName = '';
        $expectedResult = 'app_';

        // Act
        $result = $this->formatter->format($indexName);

        // Assert
        $this->assertEquals($expectedResult, $result, 'Formatter should handle empty index name');
    }
}
