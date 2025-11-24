<?php
/**
 * Copyright © Graycore. All rights reserved.
 */
declare(strict_types=1);

namespace Graycore\CmsAiBuilder\Test\Unit\Service;

use Graycore\CmsAiBuilder\Service\PatchApplier;
use Magento\Framework\Serialize\Serializer\Json;
use PHPUnit\Framework\TestCase;

class PatchApplierTest extends TestCase
{
    /**
     * @var PatchApplier
     */
    private PatchApplier $patchApplier;

    /**
     * Set up test dependencies
     */
    protected function setUp(): void
    {
        $json = new Json();
        $this->patchApplier = new PatchApplier($json);
    }

    public function testApplyOperationsToEmptySchema() {
        $operations = '[
            {"op":"replace","path":"","value":{"type":"elementSchema","element":"div","styles":{"base":{"padding":"40px","maxWidth":"800px","margin":"0 auto"}},"children":[{"type":"elementSchema","element":"h1","styles":{"base":{"fontSize":"2rem","margin":"0 0 16px"}},"children":[{"type":"textSchema","text":"Welcome to v8"}]},{"type":"elementSchema","element":"p","styles":{"base":{"fontSize":"1rem","margin":"0 0 12px"}},"children":[{"type":"textSchema","text":"This is a redesigned starter page for v8."}]},{"type":"elementSchema","element":"a","styles":{"base":{"color":"#1a0dab","textDecoration":"underline"}},"children":[{"type":"textSchema","text":"Learn more"}]}]}}
        ]';
        $schema = '{}';

        $result = $this->patchApplier->applyPatch($schema, json_decode($operations, true));

        $this->assertNotEquals($result, '[]');
        $this->assertNotEquals($result, []);

        $this->assertEquals(json_encode($result), '{"type":"elementSchema","element":"div","styles":{"base":{"padding":"40px","maxWidth":"800px","margin":"0 auto"}},"children":[{"type":"elementSchema","element":"h1","styles":{"base":{"fontSize":"2rem","margin":"0 0 16px"}},"children":[{"type":"textSchema","text":"Welcome to v8"}]},{"type":"elementSchema","element":"p","styles":{"base":{"fontSize":"1rem","margin":"0 0 12px"}},"children":[{"type":"textSchema","text":"This is a redesigned starter page for v8."}]},{"type":"elementSchema","element":"a","styles":{"base":{"color":"#1a0dab","textDecoration":"underline"}},"children":[{"type":"textSchema","text":"Learn more"}]}]}');
    }
}
