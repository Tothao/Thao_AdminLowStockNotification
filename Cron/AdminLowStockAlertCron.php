<?php

namespace Thao\AdminLowStockNotification\Cron;

use Thao\AdminLowStockNotification\Model\ResourceModel\AdminLowStockNotification\CollectionFactory;
use Thao\AdminLowStockNotification\Helper\Data;
class AdminLowStockAlertCron
{
    protected $adminLowStockCollectionFactory;
    protected $helper;

    public function __construct(
        CollectionFactory $adminLowStockCollectionFactory,
        Data            $helper
    ) {
        $this->adminLowStockCollectionFactory = $adminLowStockCollectionFactory;
        $this->helper = $helper;
    }

    public function execute()
    {
        $isEnableAdminLowStockNotification = $this->helper->isEnableAdminLowStockNotification();
        if(!$isEnableAdminLowStockNotification){
            return;
        }
        $adminLowStockCollection = $this->adminLowStockCollectionFactory->create()
            ->addFieldToFilter('status', 0);


        if(!$adminLowStockCollection->getSize()){
            return;
        }
    }

    public function sendMail()
    {
        $senderEmail = $this->scopeConfig
            ->getValue('trans_email/ident_general/email', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $senderName = $this->scopeConfig
            ->getValue('trans_email/ident_general/name', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        $this->inlineTranslation->suspend();
        $sender = [
            'name' => $senderName,
            'email' => $senderEmail,
        ];
    }

}
