<?php

namespace Thao\AdminLowStockNotification\Cron;

use Thao\AdminLowStockNotification\Model\ResourceModel\AdminLowStockNotification\CollectionFactory;
use Thao\AdminLowStockNotification\Helper\Data;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Store\Model\StoreManagerInterface;
class AdminLowStockAlertCron
{
    protected $adminLowStockCollectionFactory;
    protected $helper;
    protected $scopeConfig;
    protected $inlineTranslation;
    protected $transportBuilder;
    protected $storeManager;


    public function __construct(
        CollectionFactory $adminLowStockCollectionFactory,
        Data            $helper,
        ScopeConfigInterface $scopeConfig,
        StateInterface $inlineTranslation,
        TransportBuilder $transportBuilder,
        StoreManagerInterface $storeManager

    ) {
        $this->adminLowStockCollectionFactory = $adminLowStockCollectionFactory;
        $this->helper = $helper;
        $this->scopeConfig = $scopeConfig;
        $this->inlineTranslation = $inlineTranslation;
        $this->transportBuilder = $transportBuilder;
        $this->storeManager = $storeManager;
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
        $this->sendMail();
    }

    public function sendMail()
    {
        $senderEmail = $this->scopeConfig
            ->getValue('trans_email/ident_general/email', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $senderName = $this->scopeConfig
            ->getValue('trans_email/ident_general/name', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $setTemplateVar = $this->helper->getList();

        $this->inlineTranslation->suspend();

        $sendToEmail = $this->helper->getAdminEmail();
        $sender = [
            'name' => $senderName,
            'email' => $senderEmail,
        ];
        $transport = $this->transportBuilder
            ->setTemplateIdentifier('AdminLowStockAlertCron_general_email_template')
            ->setTemplateOptions(
                    [
                        'area' => \Magento\Framework\App\Area::AREA_ADMINHTML,
                        'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                    ]
            )
            ->setTemplateVars($setTemplateVar)
        ->setFrom($sender)
        ->addTo($sendToEmail)
        ->getTransport();
        $transport->sendMessage();
    }

}
