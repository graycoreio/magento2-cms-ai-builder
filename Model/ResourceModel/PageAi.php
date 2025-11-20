<?php
/**
 * Copyright © Graycore, LLC. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Graycore\CmsAiBuilder\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * CMS Page AI Resource Model
 */
class PageAi extends AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('cms_page_ai', 'page_id');
    }
}
