<?php
/**
 * Copyright © Graycore. All rights reserved.
 */
declare(strict_types=1);

namespace Graycore\CmsAiBuilder\Test\Unit\Schema\ContentSchema;

use Graycore\CmsAiBuilder\Service\Schema\ComponentSchema;
use Graycore\CmsAiBuilder\Service\Schema\JsonPatchResponseSchema;
use JsonSchema\Validator;
use PHPUnit\Framework\TestCase;

class ContentSchemaTest extends TestCase
{
    private JsonPatchResponseSchema $schema;
    private Validator $validator;
    private array $contentSchemaDefinition;

    public function setUp(): void
    {
        $componentSchema = new ComponentSchema();
        $this->schema = new JsonPatchResponseSchema($componentSchema);
        $this->validator = new Validator();

        // Get the DaffContentSchema definition for validation
        $fullSchema = $this->schema->getSchema();
        $this->contentSchemaDefinition = $fullSchema['schema']['$defs'];
    }

    /**
     * Load a schema from a JSON file
     *
     * @param string $filePath
     * @return string
     */
    private static function loadSchema(string $filePath): string
    {
        if (!file_exists($filePath)) {
            throw new \Exception("Schema file not found: {$filePath}");
        }

        $json = file_get_contents($filePath);
        if ($json === false) {
            throw new \Exception("Failed to read schema file: {$filePath}");
        }

        return $json;
    }

    /**
     * Data provider for content schema test cases
     *
     * @return array<string, array{name: string, content: string, schemaType: string}>
     */
    public static function contentSchemaProvider(): array
    {
        $examplesDir = __DIR__ . '/example_schemas';

        return [
            'text_schema' => [
                'name' => 'Text Schema',
                'content' => self::loadSchema("{$examplesDir}/text_schema.json"),
                'schemaType' => 'DaffTextSchema'
            ],
            'element_schema_simple' => [
                'name' => 'Simple Element Schema',
                'content' => self::loadSchema("{$examplesDir}/element_schema_simple.json"),
                'schemaType' => 'DaffContentElementSchema'
            ],
            'element_schema_with_styles' => [
                'name' => 'Element Schema with Styles',
                'content' => self::loadSchema("{$examplesDir}/element_schema_with_styles.json"),
                'schemaType' => 'DaffContentElementSchema'
            ],
            'hero_component_schema' => [
                'name' => 'Hero Component Schema',
                'content' => self::loadSchema("{$examplesDir}/hero_component_schema.json"),
                'schemaType' => 'DaffHeroComponentSchema'
            ],
            'nested_element_schema' => [
                'name' => 'Nested Element Schema',
                'content' => self::loadSchema("{$examplesDir}/nested_element_schema.json"),
                'schemaType' => 'DaffContentElementSchema'
            ]
        ];
    }

    /**
     * Data provider for failing content schema test cases
     *
     * @return array<string, array{name: string, content: string, schemaType: string, expectedError: string}>
     */
    public static function failingContentSchemaProvider(): array
    {
        $examplesDir = __DIR__ . '/example_failing_schemas';

        return [
            'invalid_hero_input' => [
                'name' => 'Hero with invalid input (headline)',
                'content' => self::loadSchema("{$examplesDir}/simple_page.json"),
                'schemaType' => 'DaffContentElementSchema',
                'expectedError' => 'headline'
            ]
        ];
    }

    /**
     * Test content schema validation with data provider
     *
     * @dataProvider contentSchemaProvider
     */
    public function testContentSchemaValidation(string $name, string $content, string $schemaType): void
    {
        // Get the specific schema definition
        if (!isset($this->contentSchemaDefinition[$schemaType])) {
            $this->fail("Schema type '{$schemaType}' not found in definitions");
        }

        $schemaDefinition = $this->contentSchemaDefinition[$schemaType];

        // Create a complete schema object with $defs for validation
        $validationSchema = [
            'type' => 'object',
            '$defs' => $this->contentSchemaDefinition
        ] + $schemaDefinition;

        // Convert to objects for validation
        $contentObject = json_decode($content);
        $schemaObject = json_decode(json_encode($validationSchema));

        // Validate
        $this->validator->validate($contentObject, $schemaObject);

        if ($this->validator->isValid()) {
            $this->assertTrue(true);
        } else {
            foreach ($this->validator->getErrors() as $error) {
                echo sprintf("  - [%s] %s\n", $error['property'], $error['message']);
            }
            $this->validator->reset();
            $this->fail("Content schema validation failed for: {$name}");
        }

        $this->validator->reset();
    }

    /**
     * Test that invalid content schemas fail validation
     *
     * @dataProvider failingContentSchemaProvider
     */
    public function testFailingContentSchemaValidation(string $name, string $content, string $schemaType, string $expectedError): void
    {
        // Get the specific schema definition
        if (!isset($this->contentSchemaDefinition[$schemaType])) {
            $this->fail("Schema type '{$schemaType}' not found in definitions");
        }

        $schemaDefinition = $this->contentSchemaDefinition[$schemaType];

        // Create a complete schema object with $defs for validation
        $validationSchema = [
            'type' => 'object',
            '$defs' => $this->contentSchemaDefinition
        ] + $schemaDefinition;

        // Convert to objects for validation
        $contentObject = json_decode($content);
        $schemaObject = json_decode(json_encode($validationSchema));

        // Validate
        $this->validator->validate($contentObject, $schemaObject);

        // This test expects validation to FAIL
        if ($this->validator->isValid()) {
            $this->validator->reset();
            $this->fail("Expected validation to fail for: {$name}, but it passed");
        }

        // Get validation errors
        $errors = $this->validator->getErrors();
        $errorMessages = array_map(fn($e) => $e['property'] . ': ' . $e['message'], $errors);

        // Assert that we have at least one error
        if (count($errors) === 0) {
            $this->validator->reset();
            $this->fail("Expected validation to fail for: {$name}, but no errors were found");
        }

        // Check that the FIRST error is about the expected property
        $firstError = $errors[0];
        $foundExpectedError = stripos($firstError['property'], $expectedError) !== false ||
                             stripos($firstError['message'], $expectedError) !== false;

        if (!$foundExpectedError) {
            $this->validator->reset();
            $this->fail("Expected first error containing '{$expectedError}' not found for: {$name}");
        }

        $this->validator->reset();
        $this->assertTrue(true);
    }
}
