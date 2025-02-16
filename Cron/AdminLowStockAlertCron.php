<?php

namespace Thao\AdminLowStockNotification\Cron;

use Thao\AdminLowStockNotification\Helper\Data;

class AdminLowStockAlertCron
{
    /**
     * @var Data
     */
    protected $helper;

    /**
     * @param Data $helper
     */
    public function __construct(Data $helper)
    {
        $this->helper = $helper;
    }

    public function execute()
    {
        $this->helper->sendAdminLowStockAlert();
    }
}
