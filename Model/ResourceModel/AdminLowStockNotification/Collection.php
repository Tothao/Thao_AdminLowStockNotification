<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Thao\AdminLowStockNotification\Model\ResourceModel\AdminLowStockNotification;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{

    /**
     * @inheritDoc
     */
    protected $_idFieldName = 'low_stock_id';

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(
            \Thao\AdminLowStockNotification\Model\AdminLowStockNotification::class,
            \Thao\AdminLowStockNotification\Model\ResourceModel\AdminLowStockNotification::class
        );
    }
}

