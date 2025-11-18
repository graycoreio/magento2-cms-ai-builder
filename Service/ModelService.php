<?php
/**
 * Copyright © Graycore. All rights reserved.
 */
declare(strict_types=1);

namespace Graycore\CmsAiBuilder\Service;

use Graycore\CmsAiBuilder\Helper\Config;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\Serialize\Serializer\Json;
use Psr\Log\LoggerInterface;

class ModelService
{
    private const OPENAI_API_URL = 'https://api.openai.com/v1/chat/completions';

    public function __construct(
        private readonly Config $config,
        private readonly Curl $curl,
        private readonly Json $json,
        private readonly LoggerInterface $logger
    ) {
    }

    /**
     * Call OpenAI API with messages
     *
     * @param array $messages Array of message objects with 'role' and 'content'
     * @param int|null $storeId
     * @return array The parsed response content
     * @throws \Exception
     */
    public function callModel(array $messages, ?int $storeId = null): array
    {
        if (!$this->config->isEnabled($storeId)) {
            throw new \Exception('AI CMS Builder is not enabled');
        }

        $apiKey = $this->config->getOpenAiApiKey();
        if (!$apiKey) {
            throw new \Exception('OpenAI API key is not configured');
        }

        $payload = [
            'model' => 'gpt-5-nano',
            'messages' => $messages,
            'response_format' => ['type' => 'json_object']
        ];

        try {
            $this->curl->addHeader('Content-Type', 'application/json');
            $this->curl->addHeader('Authorization', 'Bearer ' . $apiKey);
            $this->curl->post(self::OPENAI_API_URL, $this->json->serialize($payload));

            $response = $this->curl->getBody();
            $responseData = $this->json->unserialize($response);

            if (isset($responseData['error'])) {
                throw new \Exception('OpenAI API error: ' . $responseData['error']['message']);
            }

            if (!isset($responseData['choices'][0]['message']['content'])) {
                throw new \Exception('Invalid response from OpenAI API');
            }

            $content = $responseData['choices'][0]['message']['content'];
            return $this->json->unserialize($content);

        } catch (\Exception $e) {
            $this->logger->error('Failed to call model: ' . $e->getMessage());
            throw $e;
        }
    }
}
