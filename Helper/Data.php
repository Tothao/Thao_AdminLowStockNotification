<?php
namespace Thao\AdminLowStockNotification\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\View\Element\Template;
use Thao\AdminLowStockNotification\Model\ResourceModel\AdminLowStockNotification\CollectionFactory;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;


class Data extends AbstractHelper
{
    protected $transportBuilder;
    protected $scopeConfig;
    protected $storeManager;
    protected $productRepository;
    protected  $adminLowStockCollection;
    protected $inlineTranslation;


    public function __construct(
        TransportBuilder $transportBuilder,
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        StateInterface $inlineTranslation,
        CollectionFactory $adminLowStockCollection,
        ProductRepositoryInterface $productRepository

    ) {
        $this->transportBuilder = $transportBuilder;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->inlineTranslation = $inlineTranslation;
        $this->adminLowStockCollection = $adminLowStockCollection;
        $this->productRepository = $productRepository;
    }

    public function isEnableAdminLowStockNotification( )
    {
        $valueFromConfig = $this->scopeConfig->getValue(
            'admin_low_stock_notification/general/enable',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
        );
        return $valueFromConfig;
    }
    public function getLowStock(){
        $lowStock = $this->scopeConfig->getValue(
            'admin_low_stock_notification/general/low_stock_threshold',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
        );
        return $lowStock;
    }

    public function getAdminEmail(){
        $email = $this->scopeConfig->getValue(
            'admin_low_stock_notification/general/notification_email',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
        );
        return $email;
    }
    public function getList()
    {
        $adminLowStockCollection = $this->adminLowStockCollection->create()
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


