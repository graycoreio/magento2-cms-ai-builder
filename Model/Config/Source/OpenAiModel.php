<?php
/**
 * Copyright © Graycore. All rights reserved.
 */
declare(strict_types=1);

namespace Graycore\CmsAiBuilder\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class OpenAiModel implements OptionSourceInterface
{
    /**
     * Get available OpenAI models as option array
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => 'gpt-5-nano', 'label' => __('GPT-5 Nano')],
            ['value' => 'gpt-5-mini', 'label' => __('GPT-5 Mini')],
            ['value' => 'gpt-5', 'label' => __('GPT-5')],
            ['value' => 'gpt-4o', 'label' => __('GPT-4o')],
            ['value' => 'gpt-4o-mini', 'label' => __('GPT-4o Mini')],
            ['value' => 'gpt-4-turbo', 'label' => __('GPT-4 Turbo')],
        ];
    }
}