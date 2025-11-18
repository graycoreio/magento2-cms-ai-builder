<?php
/**
 * Copyright © Graycore. All rights reserved.
 */
declare(strict_types=1);

namespace Graycore\CmsAiBuilder\Controller\Adminhtml\Generate;

use Graycore\CmsAiBuilder\Service\PatchGenerator;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Cms\Api\PageRepositoryInterface;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Serialize\Serializer\Json;

class Index extends Action implements HttpPostActionInterface
{
    /**
     * Authorization level of a basic admin session
     */
    const ADMIN_RESOURCE = 'Magento_Cms::page';

    /**
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param PatchGenerator $patchGenerator
     * @param PageRepositoryInterface $pageRepository
     * @param Json $json
     */
    public function __construct(
        Context $context,
        private readonly JsonFactory $resultJsonFactory,
        private readonly PatchGenerator $patchGenerator,
        private readonly PageRepositoryInterface $pageRepository,
        private readonly Json $json
    ) {
        parent::__construct($context);
    }

    /**
     * Execute action
     */
    public function execute()
    {
        $resultJson = $this->resultJsonFactory->create();

        try {
            $request = $this->getRequest();
            $prompt = $request->getParam('prompt');
            $schema = $request->getParam('schema');
            $pageId = $request->getParam('page_id');
            $conversationHistoryJson = $request->getParam('conversation_history');

            if (!$prompt || empty(trim($prompt))) {
                return $resultJson->setData([
                    'success' => false,
                    'error' => 'Prompt is required'
                ]);
            }

            $conversationHistory = null;

            // If page exists, load conversation history from database
            if ($pageId) {
                try {
                    $page = $this->pageRepository->getById($pageId);
                    $storedHistoryJson = $page->getData('ai_conversation_history');
                    if ($storedHistoryJson) {
                        try {
                            $conversationHistory = $this->json->unserialize($storedHistoryJson);
                        } catch (\Exception $e) {
                            // Invalid JSON, start fresh
                            $conversationHistory = null;
                        }
                    }
                } catch (\Exception $e) {
                    // Page not found, fall through to use request history
                }
            }

            // If no page or no stored history, use conversation history from request
            if ($conversationHistory === null && $conversationHistoryJson) {
                try {
                    $conversationHistory = $this->json->unserialize($conversationHistoryJson);
                } catch (\Exception $e) {
                    // Invalid JSON, start fresh
                    $conversationHistory = null;
                }
            }

            // Generate schema with conversation history
            $result = $this->patchGenerator->generateSchema($prompt, $schema, $conversationHistory);

            // Save conversation history to database if page exists
            if ($pageId && isset($page)) {
                $page->setData('ai_conversation_history', $this->json->serialize($result->getConversationHistory()));
                $this->pageRepository->save($page);
            }

            return $resultJson->setData([
                'success' => true,
                'schema' => $result->getDynamicSchema(),
                'conversationHistory' => $result->getConversationHistory()
            ]);

        } catch (\Exception $e) {
            return $resultJson->setData([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }
}
