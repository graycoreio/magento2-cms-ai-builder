<?php
/**
 * Copyright © Graycore. All rights reserved.
 */
declare(strict_types=1);

namespace Graycore\CmsAiBuilder\Service\Schema;

/**
 * JSON Schema for RFC 6902 JSON Patch response format
 */
class JsonPatchResponseSchema
{
    public function __construct(
        private readonly ComponentSchema $componentSchema
    ) {
    }
    /**
     * Get the schema definitions for DaffContentSchema types
     *
     * @return array
     */
    public function getDaffContentSchemaDefinitions(): array
    {
        return [
            'DaffContentSchema' => [
                'anyOf' => [
                    ['$ref' => '#/$defs/DaffTextSchema'],
                    ['$ref' => '#/$defs/DaffContentElementSchema'],
                    ['$ref' => '#/$defs/DaffContentComponentSchema']
                ]
            ],
            'DaffTextSchema' => [
                'type' => 'object',
                'properties' => [
                    'type' => ['type' => 'string', 'const' => 'textSchema'],
                    'text' => ['type' => 'string']
                ],
                'required' => ['type', 'text'],
                'additionalProperties' => false
            ],
            'DaffContentElementSchema' => [
                'type' => 'object',
                'properties' => [
                    'type' => ['type' => 'string', 'const' => 'elementSchema'],
                    'element' => ['type' => 'string'],
                    'attributes' => [
                        'type' => 'object',
                        'additionalProperties' => ['type' => 'string']
                    ],
                    'children' => [
                        'type' => 'array',
                        'items' => ['$ref' => '#/$defs/DaffContentSchema']
                    ],
                    'styles' => [
                        'type' => 'object',
                        'properties' => [
                            'base' => [
                                'type' => 'object',
                                'additionalProperties' => [
                                    'anyOf' => [
                                        ['type' => 'string'],
                                        ['type' => 'number']
                                    ]
                                ]
                            ],
                            'breakpoints' => [
                                'type' => 'object',
                                'additionalProperties' => [
                                    'type' => 'object',
                                    'additionalProperties' => [
                                        'anyOf' => [
                                            ['type' => 'string'],
                                            ['type' => 'number']
                                        ]
                                    ]
                                ]
                            ]
                        ],
                        'required' => [],
                        'additionalProperties' => false
                    ]
                ],
                'required' => ['type', 'element', 'children', 'styles'],
                'additionalProperties' => false
            ],
            'DaffContentComponentSchema' => [
                'anyOf' => array_map(
                    fn($schemaName) => ['$ref' => "#/\$defs/{$schemaName}"],
                    array_keys($this->componentSchema->getSchemas())
                )
            ]
        ] + $this->componentSchema->getSchemas();
    }

    /**
     * Get the JSON schema for structured outputs (RFC 6902 JSON Patch)
     *
     * @return array
     */
    public function getSchema(): array
    {
        return [
            'type' => 'json_schema',
            'name' => 'json_patch_response',
            'schema' => [
                'type' => 'object',
                '$defs' => array_merge(
                    $this->getDaffContentSchemaDefinitions(),
                    [
                        'JsonValue' => [
                            'anyOf' => [
                                ['type' => 'string'],
                                ['type' => 'number'],
                                ['type' => 'boolean'],
                                ['type' => 'null'],
                                ['$ref' => '#/$defs/DaffContentSchema']
                            ]
                        ]
                    ]
                ),
                'properties' => [
                    'reply' => [
                        'type' => 'string',
                        'description' => 'Brief explanation of changes made'
                    ],
                    'patch' => [
                        'type' => 'array',
                        'description' => 'Array of JSON Patch operations (RFC 6902)',
                        'items' => [
                            'type' => 'object',
                            'properties' => [
                                'op' => [
                                    'type' => 'string',
                                    'enum' => ['add', 'remove', 'replace', 'move', 'copy', 'test'],
                                    'description' => 'The operation to perform'
                                ],
                                'path' => [
                                    'type' => 'string',
                                    'description' => 'JSON Pointer to the target location'
                                ],
                                'value' => [
                                    '$ref' => '#/$defs/JsonValue'
                                ],
                                'from' => [
                                    'type' => 'string',
                                    'description' => 'JSON Pointer to source location (required for move and copy operations, empty string for others)'
                                ]
                            ],
                            'required' => ['op', 'path', 'value', 'from'],
                            'additionalProperties' => false
                        ]
                    ]
                ],
                'required' => ['reply', 'patch'],
                'additionalProperties' => false
            ]
        ];
    }
}
