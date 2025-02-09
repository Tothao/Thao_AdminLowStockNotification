<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Thao\AdminLowStockNotification\Model;

use Magento\Framework\Model\AbstractModel;
use Thao\Blog\Api\Data\PostInterface;

class AdminLowStockNotification extends AbstractModel
{
    public const LOW_STOCK_ID = 'low_stock_id';
    public const PRODUCT_ID = 'product_id';
    public const WEBSITE_ID = 'website_id';
    public const STORE_ID = 'store_id';
    public const ADD_DATE = 'add_date';
    public const SEND_DATE = 'send_date';
    public const SEND_COUNT = 'send_count';
    public const STATUS = 'status';
    public const STOCK_ID = 'stock_id';
    public const  REMAINING_QUANTITY = 'remaining_quantity';

    /**
     * @inheritDoc
     */
    public function _construct()
    {
        $this->_init(\Thao\AdminLowStockNotification\Model\ResourceModel\AdminLowStockNotification::class);
    }

    /**
     * @inheritDoc
     */
    public function getLowStockId()
    {
        return $this->getData(self::LOW_STOCK_ID);
    }

    /**
     * @inheritDoc
     */
    public function setLowStockId($lowStockId)
    {
        return $this->setData(self::LOW_STOCK_ID, $lowStockId);
    }

    /**
     * @inheritDoc
     */
    public function getProductId()
    {
        return $this->getData(self::PRODUCT_ID);
    }

    /**
     * @inheritDoc
     */
    public function setProductId($productId)
    {
        return $this->setData(self::PRODUCT_ID, $productId);
    }

    /**
     * @inheritDoc
     */
    public function getWebsiteId()
    {
        return $this->getData(self::WEBSITE_ID);
    }

    /**
     * @inheritDoc
     */
    public function setWebsiteId($websiteId)
    {
        return $this->setData(self::WEBSITE_ID ,$websiteId);

    }
    /**
     * @inheritDoc
     */
    public function getStoreId()
    {
        return $this->getData(self::STORE_ID);
    }

    /**
     * @inheritDoc
     */
    public function setStoreId($storeId)
    {
        return $this->setData(self::STORE_ID, $storeId);
    }

    /**
     * @inheritDoc
     */
    public function getAddDate()
    {
        return $this->getData(self::ADD_DATE);
    }

    /**
     * @inheritDoc
     */
    public function setAddDate($addDate)
    {
        return $this->setData(self::ADD_DATE, $addDate);
    }

    /**
     * @inheritDoc
     */
    public function getSendDate()
    {
        return $this->getData(self::SEND_DATE);
    }

    /**
     * @inheritDoc
     */
    public function setSendDate($sendDate)
    {
        return $this->setData(self::SEND_DATE, $sendDate);
    }

    /**
     * @inheritDoc
     */
    public function getSendCount()
    {
        return $this->getData(self::SEND_COUNT);
    }

    /**
     * @inheritDoc
     */
    public function setSendCount($sendCount)
    {
        return $this->setData(self::SEND_COUNT, $sendCount);
    }

    /**
     * @inheritDoc
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * @inheritDoc
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }
    /**
     * @inheritDoc
     */
    public function setStockId($stockId)
    {
        return $this->setData(self::STOCK_ID, $stockId);
    }

    /**
     * @inheritDoc
     */
    public function getStockId()
    {
        return $this->getData(self::STOCK_ID);
    }

    public function setRemainingQuantity($remainingQuantity)
    {
        return $this->setData(self::REMAINING_QUANTITY, $remainingQuantity);
    }

    public function getRemainingQuantity()
    {
        return $this->getData(self::REMAINING_QUANTITY);
    }
}

