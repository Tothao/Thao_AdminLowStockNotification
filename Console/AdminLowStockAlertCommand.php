<?php
namespace Thao\AdminLowStockNotification\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Thao\AdminLowStockNotification\Helper\Data;

class AdminLowStockAlertCommand extends Command
{
    protected $helper;

    protected function configure()
    {
        $this->setName('lowstock:send');
        $this->setDescription('Send email low stock to admin');

        parent::configure();
    }

    public function __construct(
        Data $helper,
        ?string $name = null
    ) {
        $this->helper = $helper;
        parent::__construct($name);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->helper->sendAdminLowStockAlert();
        $output->writeln("Send Alert Done!");
        return 1;
    }
}
