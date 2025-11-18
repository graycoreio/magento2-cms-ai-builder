<?php
/**
 * Copyright © Graycore. All rights reserved.
 */
declare(strict_types=1);

namespace Graycore\CmsAiBuilder\Test\Unit\Service;

use Graycore\CmsAiBuilder\Service\PatchApplier;
use Magento\Framework\Serialize\Serializer\Json;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class PatchApplierTest extends TestCase
{
    /**
     * @var PatchApplier
     */
    private PatchApplier $patchApplier;

    /**
     * @var Json|MockObject
     */
    private $jsonMock;

    /**
     * Set up test dependencies
     */
    protected function setUp(): void
    {
        $this->jsonMock = $this->createMock(Json::class);
        $this->patchApplier = new PatchApplier($this->jsonMock);
    }

    /**
     * Test successful patch application with add operation
     */
    public function testApplyPatchWithAddOperation(): void
    {
        $schema = [
            'title' => 'Test Schema',
            'components' => []
        ];

        $patchOperations = [
            [
                'op' => 'add',
                'path' => '/components/0',
                'value' => ['type' => 'button', 'label' => 'Click Me']
            ]
        ];

        $expectedResult = [
            'title' => 'Test Schema',
            'components' => [
                ['type' => 'button', 'label' => 'Click Me']
            ]
        ];

        $this->jsonMock->expects($this->once())
            ->method('unserialize')
            ->willReturn($expectedResult);

        $result = $this->patchApplier->applyPatch($schema, $patchOperations);

        $this->assertEquals($expectedResult, $result);
    }

    /**
     * Test successful patch application with replace operation
     */
    public function testApplyPatchWithReplaceOperation(): void
    {
        $schema = [
            'title' => 'Old Title',
            'version' => '1.0'
        ];

        $patchOperations = [
            [
                'op' => 'replace',
                'path' => '/title',
                'value' => 'New Title'
            ]
        ];

        $expectedResult = [
            'title' => 'New Title',
            'version' => '1.0'
        ];

        $this->jsonMock->expects($this->once())
            ->method('unserialize')
            ->willReturn($expectedResult);

        $result = $this->patchApplier->applyPatch($schema, $patchOperations);

        $this->assertEquals($expectedResult, $result);
    }

    /**
     * Test successful patch application with remove operation
     */
    public function testApplyPatchWithRemoveOperation(): void
    {
        $schema = [
            'title' => 'Test Schema',
            'deprecated' => true,
            'version' => '1.0'
        ];

        $patchOperations = [
            [
                'op' => 'remove',
                'path' => '/deprecated'
            ]
        ];

        $expectedResult = [
            'title' => 'Test Schema',
            'version' => '1.0'
        ];

        $this->jsonMock->expects($this->once())
            ->method('unserialize')
            ->willReturn($expectedResult);

        $result = $this->patchApplier->applyPatch($schema, $patchOperations);

        $this->assertEquals($expectedResult, $result);
    }

    /**
     * Test multiple patch operations applied in sequence
     */
    public function testApplyPatchWithMultipleOperations(): void
    {
        $schema = [
            'title' => 'Original',
            'items' => ['a', 'b']
        ];

        $patchOperations = [
            [
                'op' => 'replace',
                'path' => '/title',
                'value' => 'Modified'
            ],
            [
                'op' => 'add',
                'path' => '/items/2',
                'value' => 'c'
            ]
        ];

        $expectedResult = [
            'title' => 'Modified',
            'items' => ['a', 'b', 'c']
        ];

        $this->jsonMock->expects($this->once())
            ->method('unserialize')
            ->willReturn($expectedResult);

        $result = $this->patchApplier->applyPatch($schema, $patchOperations);

        $this->assertEquals($expectedResult, $result);
    }

    /**
     * Test copy operation
     */
    public function testApplyPatchWithCopyOperation(): void
    {
        $schema = [
            'original' => 'value',
            'nested' => ['key' => 'data']
        ];

        $patchOperations = [
            [
                'op' => 'copy',
                'from' => '/original',
                'path' => '/copied'
            ]
        ];

        $expectedResult = [
            'original' => 'value',
            'copied' => 'value',
            'nested' => ['key' => 'data']
        ];

        $this->jsonMock->expects($this->once())
            ->method('unserialize')
            ->willReturn($expectedResult);

        $result = $this->patchApplier->applyPatch($schema, $patchOperations);

        $this->assertEquals($expectedResult, $result);
    }

    /**
     * Test move operation
     */
    public function testApplyPatchWithMoveOperation(): void
    {
        $schema = [
            'oldLocation' => 'value',
            'other' => 'data'
        ];

        $patchOperations = [
            [
                'op' => 'move',
                'from' => '/oldLocation',
                'path' => '/newLocation'
            ]
        ];

        $expectedResult = [
            'newLocation' => 'value',
            'other' => 'data'
        ];

        $this->jsonMock->expects($this->once())
            ->method('unserialize')
            ->willReturn($expectedResult);

        $result = $this->patchApplier->applyPatch($schema, $patchOperations);

        $this->assertEquals($expectedResult, $result);
    }

    /**
     * Test applying patch to empty schema
     */
    public function testApplyPatchToEmptySchema(): void
    {
        $schema = [];
        $patchOperations = [
            [
                'op' => 'add',
                'path' => '/title',
                'value' => 'New Title'
            ]
        ];

        $expectedResult = ['title' => 'New Title'];

        $this->jsonMock->expects($this->once())
            ->method('unserialize')
            ->willReturn($expectedResult);

        $result = $this->patchApplier->applyPatch($schema, $patchOperations);

        $this->assertEquals($expectedResult, $result);
    }
}
