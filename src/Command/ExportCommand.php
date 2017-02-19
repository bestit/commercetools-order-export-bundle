<?php

namespace BestIt\CtOrderExportBundle\Command;

use BestIt\CtOrderExportBundle\Exporter;
use BestIt\CtOrderExportBundle\OrderVisitor;
use BestIt\CtOrderExportBundle\ProgressBarFactory;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * The export command for orders.
 * @author blange <lange@bestit-online.de>
 * @package BestIt\CtOrderExportBundle
 * @subpackage Command
 * @version $id$
 */
class ExportCommand extends ContainerAwareCommand
{
    /**
     * The exporter service.
     * @var Exporter
     */
    protected $exporter = null;

    /**
     * Iterator for the orders.
     * @var OrderVisitor
     */
    protected $orders = null;

    /**
     * Configures the command.
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('best-it:order-export:export-orders')
            ->setDescription('Exports the found orders to the given export folder.');
    }

    /**
     * Executes the command.
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $orderVisitor = $this->getOrders();

        $this->getExporter()->exportOrders($orderVisitor, $this->getProgressBar($output, count($orderVisitor)));
    }

    /**
     * Returns the exporter service.
     * @return Exporter
     */
    public function getExporter(): Exporter
    {
        if (!$this->exporter) {
            $this->setExporter($this->getContainer()->get('best_it_ct_order_export.exporter'));
        }

        return $this->exporter;
    }

    /**
     * Returns the order iterator.
     * @return OrderVisitor
     */
    public function getOrders(): OrderVisitor
    {
        if (!$this->orders) {
            $this->setOrders($this->getContainer()->get('best_it_ct_order_export.order_visitor'));
        }

        return $this->orders;
    }

    /**
     * Returns the progress bar from the factory.
     * @param OutputInterface $output
     * @param int $total
     * @return ProgressBar
     */
    private function getProgressBar(OutputInterface $output, int $total): ProgressBar
    {
        return $this->getProgressBarFactory()->getProgressBar($output, $total);
    }

    /**
     * Returns the factory for the progress bar.
     * @return ProgressBarFactory
     */
    private function getProgressBarFactory(): ProgressBarFactory
    {
        return $this->getContainer()->get('best_it_ct_order_export.progress_bar_factory');
    }

    /**
     * Sets the exporter service.
     * @param Exporter $exporter
     * @return ExportCommand
     */
    public function setExporter(Exporter $exporter): ExportCommand
    {
        $this->exporter = $exporter;
        return $this;
    }

    /**
     * Sets the order iterator.
     * @param OrderVisitor $orders
     * @return ExportCommand
     */
    public function setOrders(OrderVisitor $orders): ExportCommand
    {
        $this->orders = $orders;
        return $this;
    }
}
