<?php
namespace Thao\AdminLowStockNotification\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Store\Model\StoreManagerInterface;

class Data extends AbstractHelper
{
    const PENDING_STATUS = 2;
    const REJECTED_STATUS = 0;
    const APPROVED_STATUS = 1;
    protected $transportBuilder;
    protected $scopeConfig;
    protected $storeManager;
    protected $inlineTranslation;


    public function __construct(
        TransportBuilder $transportBuilder,
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        StateInterface $inlineTranslation,

    ) {
        $this->transportBuilder = $transportBuilder;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->inlineTranslation = $inlineTranslation;
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

}

