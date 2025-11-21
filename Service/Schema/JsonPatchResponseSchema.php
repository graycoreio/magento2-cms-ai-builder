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
                                    'description' => 'The value to add, replace, or test (required for add, replace, test operations)'
                                ],
                                'from' => [
                                    'type' => 'string',
                                    'description' => 'JSON Pointer to source location (required for move and copy operations)'
                                ]
                            ],
                            'required' => ['op', 'path'],
                            'additionalProperties' => false
                        ]
                    ]
                ],
                'required' => ['reply', 'patch'],
                'additionalProperties' => false
            ],
            'strict' => true
        ];
    }
}
