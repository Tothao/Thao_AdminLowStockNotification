<?php
namespace Thao\AdminLowStockNotification\Observer;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\InventorySalesApi\Api\GetProductSalableQtyInterface;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;
use Thao\AdminLowStockNotification\Model\AdminLowStockNotificationFactory;
use Thao\AdminLowStockNotification\Model\ResourceModel\AdminLowStockNotification\CollectionFactory;
use Thao\AdminLowStockNotification\Helper\Data;




Class CheckSalableQuantity implements ObserverInterface
{

    protected $salableQty;
    protected $storeManager;
    protected $logger;
    protected $adminLowStockNotificationFactory;

    protected $salableQtyBySku;
    protected $helper;

    protected $collectionFactory;

    public function __construct(
        GetProductSalableQtyInterface $salableQty,
        StoreManagerInterface $storeManager,
        LoggerInterface $logger,
        \Magento\InventorySalesAdminUi\Model\GetSalableQuantityDataBySku $salableQtyBySku,
        AdminLowStockNotificationFactory $adminLowStockNotificationFactory,
        CollectionFactory $collectionFactory,
        Data $helper
    )
    {
        $this->salableQty = $salableQty;
        $this->storeManager = $storeManager;
        $this->logger = $logger;
        $this->salableQtyBySku = $salableQtyBySku;
        $this->adminLowStockNotificationFactory = $adminLowStockNotificationFactory;
        $this->collectionFactory = $collectionFactory;
        $this->helper = $helper;

    }

    /**
     * @param Observer $observer
     * @return void
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\InventoryConfigurationApi\Exception\SkuIsNotAssignedToStockException
     */
    public function execute(Observer $observer)
    {

        /** @var \Magento\Sales\Model\Order $order */
        $order = $observer->getEvent()->getOrder();
        $orderItems = $order->getItems();
        $isEnableAdminLowStockNotification = $this->helper->isEnableAdminLowStockNotification();
        if(!$isEnableAdminLowStockNotification){
            return;
        }
        $lowStockQty = $this->helper->getLowStock();
        foreach ($orderItems as $item) {
            $salableQuantities = $this->salableQtyBySku->execute($item->getSku());
            foreach ($salableQuantities as $salableQuantity) {
                $qty = $salableQuantity['qty'];
                if ($qty < $lowStockQty) {
                    $collection = $this->collectionFactory->create()
                        ->addFieldToFilter('product_id',$item->getProductId())
                        ->addFieldToFilter('store_id',$item->getStoreId())
                        ->addFieldToFilter('website_id',$order->getStore()->getWebsiteId())
                        ->addFieldToFilter('stock_id',$salableQuantity['stock_id'])
                        ->addFieldToFilter('status',0);

                    if ($collection->getSize()) {
                        $lowStock = $collection->getFirstItem();
                        $lowStock->setRemainingQuantity($qty);
                    } else {
                        $lowStock = $this->adminLowStockNotificationFactory->create();
                        $lowStock
                            ->setProductId($item->getProductId())
                            ->setStoreId($item->getStoreId())
                            ->setWebsiteId($order->getStore()->getWebsiteId())
                            ->setStockId($salableQuantity['stock_id'])
                            ->setRemainingQuantity($qty);
                    }

                    $lowStock->save();
                }
            }

        }


    }
}
