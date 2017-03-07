<?php

namespace BestIt\CtOrderExportBundle\Command;

use BestIt\CtOrderExportBundle\Exporter;
use BestIt\CtOrderExportBundle\OrderVisitor;
use BestIt\CtOrderExportBundle\ProgressBarFactory;
use Exception;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\LockableTrait;
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
class ExportCommand extends Command
{
    use LoggerAwareTrait, LockableTrait;

    /**
     * The exporter service.
     * @var Exporter
     */
    private $exporter = null;

    /**
     * Iterator for the orders.
     * @var OrderVisitor
     */
    private $orders = null;

    /**
     * The factory for the progress bar.
     * @var ProgressBarFactory
     */
    private $progressBarFactory = null;

    /**
     * ExportCommand constructor.
     * @param Exporter $exporter
     * @param LoggerInterface $logger
     * @param OrderVisitor $orders
     * @param ProgressBarFactory $progressBarFactory
     */
    public function __construct(
        Exporter $exporter,
        LoggerInterface $logger,
        OrderVisitor $orders,
        ProgressBarFactory $progressBarFactory
    ) {
        parent::__construct();

        $this
            ->setExporter($exporter)
            ->setOrders($orders)
            ->setProgressBarFactory($progressBarFactory)
            ->setLogger($logger);
    }

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
        $logger = $this->getLogger();

        if (!$this->lock()) {
            $output->writeln('<comment>The command is already running in another process.</comment>');
            $logger->warning('Command is already running.');
        } else {
            try {
                $logger->info('Started export in command.');

                $orderVisitor = $this->getOrders();

                $this->getExporter()->exportOrders($orderVisitor, $this->getProgressBar($output, count($orderVisitor)));

                $logger->info('Finished export in command.');
            } catch (Exception $exc) {
                $logger->error('Stopped export in command.', ['exception' => $exc]);
            }
        }
    }

    /**
     * Returns the exporter service.
     * @return Exporter
     */
    private function getExporter(): Exporter
    {
        return $this->exporter;
    }

    /**
     * Returns the used logger.
     * @return LoggerInterface
     */
    private function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    /**
     * Returns the order iterator.
     * @return OrderVisitor
     */
    private function getOrders(): OrderVisitor
    {
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
        return $this->progressBarFactory;
    }

    /**
     * Sets the exporter service.
     * @param Exporter $exporter
     * @return ExportCommand
     */
    private function setExporter(Exporter $exporter): ExportCommand
    {
        $this->exporter = $exporter;
        return $this;
    }

    /**
     * Sets the order iterator.
     * @param OrderVisitor $orders
     * @return ExportCommand
     */
    private function setOrders(OrderVisitor $orders): ExportCommand
    {
        $this->orders = $orders;
        return $this;
    }

    /**
     * Sets the progress bar factory.
     * @param ProgressBarFactory $progressBarFactory
     * @return ExportCommand
     */
    private function setProgressBarFactory(ProgressBarFactory $progressBarFactory): ExportCommand
    {
        $this->progressBarFactory = $progressBarFactory;

        return $this;
    }
}
