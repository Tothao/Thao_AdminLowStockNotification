<?php

namespace Thao\AdminLowStockNotification\Cron;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Thao\AdminLowStockNotification\Model\ResourceModel\AdminLowStockNotification\CollectionFactory;
use Thao\AdminLowStockNotification\Helper\Data;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

class AdminLowStockAlertCron
{
    protected $adminLowStockCollectionFactory;
    protected $transportBuilder;
    protected $storeManager;
    protected $inlineTranslation;
    protected $timeZone;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    protected $registry;

    /**
     * @param CollectionFactory $adminLowStockCollectionFactory
     * @param Data $helper
     * @param TransportBuilder $transportBuilder
     * @param StoreManagerInterface $storeManager
     * @param StateInterface $inlineTranslation
     * @param ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Registry $registry
     * @param TimezoneInterface $timeZone
     */
    public function __construct(
        CollectionFactory $adminLowStockCollectionFactory,
        Data            $helper,
        TransportBuilder $transportBuilder,
        StoreManagerInterface $storeManager,
        StateInterface $inlineTranslation,
        ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Registry $registry,
        TimezoneInterface $timeZone
    ) {
        $this->adminLowStockCollectionFactory = $adminLowStockCollectionFactory;
        $this->helper = $helper;
        $this->transportBuilder = $transportBuilder;
        $this->storeManager = $storeManager;
        $this->inlineTranslation = $inlineTranslation;
        $this->scopeConfig = $scopeConfig;
        $this->registry = $registry;
        $this->timeZone = $timeZone;

    }

    public function execute()
    {
        $isEnableAdminLowStockNotification = $this->helper->isEnableAdminLowStockNotification();
        if(!$isEnableAdminLowStockNotification){
            return;
        }
        $adminLowStockCollection = $this->adminLowStockCollectionFactory->create()
            ->addFieldToFilter('status', 0);

        $this->registry->register('adminLowStockCollection', $adminLowStockCollection);

        if(!$adminLowStockCollection->getSize()){
            return;
        }
        $isSendMailSuccess = $this->sendMail();
        $currentime = $this->timeZone->date()->format('Y-m-d H:i:s');
        if ($isSendMailSuccess) {
            $adminLowStockCollection->setDataToAll('status',1)
                ->setDataToAll('add_date',$currentime)
                ->save();
        }
    }

    public function sendMail()
    {
        $senderEmail = $this->scopeConfig
            ->getValue('trans_email/ident_general/email', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $senderName = $this->scopeConfig
            ->getValue('trans_email/ident_general/name', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        $this->inlineTranslation->suspend();

        $sendToEmail = $this->helper->getAdminEmail();
        $sender = [
            'name' => $senderName,
            'email' => $senderEmail,
        ];

        try {
            $transport = $this->transportBuilder
                ->setTemplateIdentifier('AdminLowStockAlertCron_general_email_template')
                ->setTemplateOptions(
                    [
                        'area' => \Magento\Framework\App\Area::AREA_ADMINHTML,
                        'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                    ]
                )
                ->setTemplateVars([
                ])
                ->setFrom($sender)
                ->addTo($sendToEmail)
                ->getTransport();
            $transport->sendMessage();
            return true;
        } catch (\Exception $e) {
            return false;
            $e->getMessage();
        }
    }

}
