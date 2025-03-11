<?php

namespace Jot\HfElastic\Tests\Unit\Migration;

use Jot\HfElastic\Migration\Mapping;
use PHPUnit\Framework\TestCase;

class MappingJsonGenerationTest extends TestCase
{
    private Mapping $mapping;
    
    protected function setUp(): void
    {
        // Carrega o mapeamento completo do arquivo de exemplo
        $this->mapping = require __DIR__ . '/../../Examples/mapping/mapping-test.php';
    }
    
    public function testJsonGeneration(): void
    {
        // Gerar o JSON do mapeamento
        $body = $this->mapping->body();
        $json = json_encode($body, JSON_PRETTY_PRINT);
        
        // Verificar se o JSON é válido
        $this->assertIsString($json);
        $this->assertNotFalse($json);
        
        // Decodificar o JSON para verificar a estrutura
        $decoded = json_decode($json, true);
        $this->assertIsArray($decoded);
        
        // Verificar elementos principais
        $this->assertArrayHasKey('index', $decoded);
        $this->assertEquals('complete-test-index', $decoded['index']);
        $this->assertArrayHasKey('body', $decoded);
        $this->assertArrayHasKey('settings', $decoded['body']);
        $this->assertArrayHasKey('mappings', $decoded['body']);
        $this->assertArrayHasKey('properties', $decoded['body']['mappings']);
    }
    
    public function testUpdateBodyJsonGeneration(): void
    {
        // Gerar o JSON do corpo de atualização
        $updateBody = $this->mapping->updateBody();
        $json = json_encode($updateBody, JSON_PRETTY_PRINT);
        
        // Verificar se o JSON é válido
        $this->assertIsString($json);
        $this->assertNotFalse($json);
        
        // Decodificar o JSON para verificar a estrutura
        $decoded = json_decode($json, true);
        $this->assertIsArray($decoded);
        
        // Verificar elementos principais
        $this->assertArrayHasKey('index', $decoded);
        $this->assertEquals('complete-test-index', $decoded['index']);
        $this->assertArrayHasKey('body', $decoded);
        $this->assertArrayHasKey('properties', $decoded['body']);
    }
    
    public function testJsonStructureDepth(): void
    {
        // Gerar o mapeamento
        $mapping = $this->mapping->generateMapping();
        
        // Verificar a profundidade da estrutura para campos aninhados
        $this->assertArrayHasKey('addresses', $mapping['properties']);
        $this->assertArrayHasKey('properties', $mapping['properties']['addresses']);
        $this->assertArrayHasKey('street', $mapping['properties']['addresses']['properties']);
        
        // Verificar a profundidade da estrutura para campos de objeto
        $this->assertArrayHasKey('contact', $mapping['properties']);
        $this->assertArrayHasKey('properties', $mapping['properties']['contact']);
        $this->assertArrayHasKey('social_media', $mapping['properties']['contact']['properties']);
        $this->assertArrayHasKey('properties', $mapping['properties']['contact']['properties']['social_media']);
        $this->assertArrayHasKey('platform', $mapping['properties']['contact']['properties']['social_media']['properties']);
    }
    
    public function testJsonSerialization(): void
    {
        // Testar o serializer JSON do mapeamento completo
        $json = json_encode($this->mapping, JSON_PRETTY_PRINT);
        
        // Verificar se o JSON é válido
        $this->assertIsString($json);
        $this->assertNotFalse($json);
        
        // Verificar se o JSON contém o nome do índice
        $this->assertStringContainsString('complete-test-index', $json);
        
        // Verificar se o JSON contém alguns tipos importantes
        $this->assertStringContainsString('"type": "text"', $json);
        $this->assertStringContainsString('"type": "keyword"', $json);
        $this->assertStringContainsString('"type": "integer"', $json);
        $this->assertStringContainsString('"type": "nested"', $json);
        $this->assertStringContainsString('"type": "geo_point"', $json);
    }
}
