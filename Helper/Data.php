<?php
namespace Thao\AdminLowStockNotification\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Store\Model\StoreManagerInterface;
use Thao\AdminLowStockNotification\Model\ResourceModel\AdminLowStockNotification\CollectionFactory;
use Magento\Framework\App\State;

class Data extends AbstractHelper
{
    protected $transportBuilder;

    protected $storeManager;
    protected $inlineTranslation;

    protected $adminLowStockCollectionFactory;
    protected $timeZone;

    /**
     * @var ScopeConfigInterface
     */

    protected $registry;

    protected $state;

    /**
     * @param Context $context
     * @param TransportBuilder $transportBuilder
     * @param StoreManagerInterface $storeManager
     * @param StateInterface $inlineTranslation
     * @param \Magento\Framework\Registry $registry
     * @param TimezoneInterface $timeZone
     * @param CollectionFactory $adminLowStockCollectionFactory
     */
    public function __construct(
        Context $context,
        TransportBuilder $transportBuilder,
        StoreManagerInterface $storeManager,
        StateInterface $inlineTranslation,
        \Magento\Framework\Registry $registry,
        TimezoneInterface $timeZone,
        CollectionFactory $adminLowStockCollectionFactory,
        State $state
    ) {
        $this->transportBuilder = $transportBuilder;
        $this->storeManager = $storeManager;
        $this->inlineTranslation = $inlineTranslation;
        $this->registry = $registry;
        $this->timeZone = $timeZone;
        $this->adminLowStockCollectionFactory = $adminLowStockCollectionFactory;
        $this->state = $state;
        parent::__construct($context);
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


    public function sendAdminLowStockAlert()
    {
        $isEnableAdminLowStockNotification = $this->isEnableAdminLowStockNotification();
        if(!$isEnableAdminLowStockNotification){
            return;
        }
        $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/admin-lowstock-alert.log');
        $logger = new \Zend_Log();
        $logger->addWriter($writer);

        $logger->info("Start send alert to admin");

        $adminLowStockCollection = $this->adminLowStockCollectionFactory->create()
            ->addFieldToFilter('status', 0);

        $this->registry->register('adminLowStockCollection', $adminLowStockCollection);

        if(!$adminLowStockCollection->getSize()){
            $logger->info("Dont have low stock");
            return;
        }
        $isSendMailSuccess = $this->sendMail();
        $currentime = $this->timeZone->date()->format('Y-m-d H:i:s');
        if ($isSendMailSuccess) {
            $adminLowStockCollection->setDataToAll('status',1)
                ->setDataToAll('add_date',$currentime)
                ->save();
            $logger->info("Done!");
        }
    }

    public function sendMail()
    {
        $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/admin-lowstock-alert.log');
        $logger = new \Zend_Log();
        $logger->addWriter($writer);



        $senderEmail = $this->scopeConfig
            ->getValue('trans_email/ident_general/email', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $senderName = $this->scopeConfig
            ->getValue('trans_email/ident_general/name', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        $this->inlineTranslation->suspend();

        $sendToEmail = $this->getAdminEmail();
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
        } catch (\Exception $exception) {
            $logger->err('Send Error: '.print_r($exception->getMessage(), true));
            $logger->err(print_r($exception->getTrace(), true));
            return false;
        }
    }

}

