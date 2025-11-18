<?php
/**
 * Copyright © Graycore. All rights reserved.
 */
declare(strict_types=1);

namespace Graycore\CmsAiBuilder\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\Serialize\Serializer\Json;

class AiSchema implements ResolverInterface
{
    /**
     * @param Json $json
     */
    public function __construct(
        private readonly Json $json
    ) {
    }

    /**
     * @inheritDoc
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        if (!isset($value['ai_schema_json']) || empty($value['ai_schema_json'])) {
            return null;
        }

        try {
            $schema = $this->json->unserialize($value['ai_schema_json']);
            return $this->formatSchema($schema);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Format schema for GraphQL response
     *
     * @param array $schema
     * @return array
     */
    private function formatSchema(array $schema): array
    {
        $formatted = [
            'component' => $schema['component'] ?? null,
            'props' => [
                'data' => isset($schema['props']) ? $this->json->serialize($schema['props']) : '{}'
            ],
            'children' => []
        ];

        if (isset($schema['children']) && is_array($schema['children'])) {
            foreach ($schema['children'] as $child) {
                $formatted['children'][] = $this->formatSchema($child);
            }
        }

        return $formatted;
    }
}
