<?php
namespace Thao\AdminLowStockNotification\Block\Adminhtml;

use Magento\Framework\View\Element\Template;
use Thao\AdminLowStockNotification\Model\ResourceModel\AdminLowStockNotification\CollectionFactory;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Thao\AdminLowStockNotification\Helper\Data;


class LowStockList extends \Magento\Framework\View\Element\Template
{
    protected $adminLowStockCollectionFactory;
    protected $productRepository;
    protected $helper;
    public function __construct(
        CollectionFactory $adminLowStockCollectionFactory,
        ProductRepositoryInterface $productRepository,
        Data            $helper,
        Template\Context $context, array $data = []
    )
    {
        $this->adminLowStockCollectionFactory = $adminLowStockCollectionFactory;
        $this->productRepository = $productRepository;
        $this->helper = $helper;
        parent::__construct($context, $data);
    }

    public function getList()
    {
        $adminLowStockCollection = $this->adminLowStockCollectionFactory->create()
            ->addFieldToFilter('status', 0);
        if (!$adminLowStockCollection->getSize()) {
            return [];
        }
        $lowStockProductsList = [];
        foreach ($adminLowStockCollection as $lowStock) {
            try {
                $product = $this->productRepository->getById($lowStock->getProductId(), false, $lowStock->getStoreId());
                $lowStockProductsList[] = [
                    'id' => $lowStock->getId(),
                    'product_id' => $product->getId(),
                    'name' => $product->getName(),
                   'remaining_quantity' => $lowStock->getRemainingQuantity(),
                   'stock_id' => $lowStock->getStockId(),
                   'store_id' => $lowStock->getStoreId(),
                    'website_id' => $lowStock->getWebsiteId(),
                ];
            } catch (NoSuchEntityException $e) {
                $this->helper->logException($e);
            }
        }return $lowStockProductsList;
    }
}
