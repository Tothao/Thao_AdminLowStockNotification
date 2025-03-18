<?php

namespace Thao\AdminLowStockNotification\Block\Adminhtml;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template;
use Thao\AdminLowStockNotification\Helper\Data;
use Thao\AdminLowStockNotification\Model\ResourceModel\AdminLowStockNotification\CollectionFactory;
class LowStockList extends \Magento\Framework\View\Element\Template
{
    /**
     * @var CollectionFactory
     */
    protected $adminLowStockCollectionFactory;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * Constructor
     *
     * @param CollectionFactory $adminLowStockCollectionFactory
     * @param ProductRepositoryInterface $productRepository
     * @param Data $helper
     * @param \Magento\Framework\Registry $registry
     * @param Template\Context $context
     * @param array $data
     */
    public function __construct(
        CollectionFactory          $adminLowStockCollectionFactory,
        ProductRepositoryInterface $productRepository,
        Data                       $helper,
        \Magento\Framework\Registry $registry,
        Template\Context           $context, array $data = []
    ){
        $this->adminLowStockCollectionFactory = $adminLowStockCollectionFactory;
        $this->productRepository = $productRepository;
        $this->helper = $helper;
        $this->registry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * @return array
     */
    public function getList()
    {
        $adminLowStockCollection = $this->registry->registry('adminLowStockCollection');
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
        }
        return $lowStockProductsList;
    }
}
