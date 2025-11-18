<?php
/**
 * Copyright © Graycore. All rights reserved.
 */
declare(strict_types=1);

namespace Graycore\CmsAiBuilder\Service;

use Magento\Framework\Serialize\Serializer\Json;
use Rs\Json\Patch;

class PatchApplier
{
    /**
     * @param Json $json
     */
    public function __construct(
        private readonly Json $json
    ) {
    }

    /**
     * Apply JSON Patch operations to a schema
     *
     * @param array $schema The current schema as an array
     * @param array $patchOperations Array of JSON Patch operations
     * @return array The patched schema as an array
     * @throws \Exception
     */
    public function applyPatch(array $schema, array $patchOperations): array
    {
        try {
            $patch = new Patch(
                json_encode($schema),
                json_encode($patchOperations)
            );

            $patchedSchemaJson = $patch->apply();
            return $this->json->unserialize($patchedSchemaJson);
        } catch (\Exception $e) {
            throw new \Exception('Failed to apply patch: ' . $e->getMessage(), 0, $e);
        }
    }
}
