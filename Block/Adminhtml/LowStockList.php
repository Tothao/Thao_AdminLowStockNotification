<?php
namespace Thao\AdminLowStockNotification\Block\Adminhtml;

use Magento\Framework\View\Element\Template;
use Thao\AdminLowStockNotification\Model\ResourceModel\AdminLowStockNotification\CollectionFactory;
use Thao\AdminLowStockNotification\Helper\Data;

class LowStockList extends \Magento\Framework\View\Element\Template
{
    protected $adminLowStockCollectionFactory;
    protected $helper;
    public function __construct(
        CollectionFactory $adminLowStockCollectionFactory,
        Data            $helper,
        Template\Context $context, array $data = []
    )
    {
        $this->adminLowStockCollectionFactory = $adminLowStockCollectionFactory;
        $this->helper = $helper;
        parent::__construct($context, $data);
    }

    public function getList()
    {
        $adminLowStockCollection = $this->adminLowStockCollectionFactory->create()
            ->addFieldToFilter('status', 0);
        return $adminLowStockCollection->getItems();
    }
}
