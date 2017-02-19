<?php

namespace BestIt\CtOrderExportBundle;

use BestIt\CtOrderExportBundle\Event\EventStore;
use BestIt\CtOrderExportBundle\Event\FinishOrderExportEvent;
use BestIt\CtOrderExportBundle\Event\PrepareOrderExportEvent;
use League\Flysystem\FilesystemInterface;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Twig_Environment;

/**
 * Exports the given orders.
 * @author blange <lange@bestit-online.de>
 * @package BestIt\CtOrderExportBundle
 * @version $id$
 */
class Exporter
{
    /**
     * The used event dispatcher.
     * @var EventDispatcherInterface
     */
    private $eventDispatcher = null;

    /**
     * The used file system.
     * @var FilesystemInterface
     */
    private $filesystem = null;

    /**
     * Which tenplate should be used for rendering.
     * @var string
     */
    private $fileTemplate = '';

    /**
     * The generator for order names.
     * @var OrderNameGenerator
     */
    private $orderNameGenerator = null;

    /**
     * The used view.
     * @var Twig_Environment
     */
    private $view = null;

    /**
     * Exporter constructor.
     * @param EventDispatcherInterface $eventDispatcher
     * @param FilesystemInterface $filesystem
     * @param string $fileTemplate
     * @param OrderNameGenerator $orderNameGenerator
     * @param Twig_Environment $view
     */
    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        FilesystemInterface $filesystem,
        string $fileTemplate,
        OrderNameGenerator $orderNameGenerator,
        Twig_Environment $view
    ) {
        $this
            ->setEventDispatcher($eventDispatcher)
            ->setFilesystem($filesystem)
            ->setFileTemplate($fileTemplate)
            ->setOrderNameGenerator($orderNameGenerator)
            ->setView($view);
    }

    /**
     * Exports the given orders.
     * @param OrderVisitor $orderVisitor
     * @param ProgressBar $bar
     * @return bool
     * @todo Add ErrorManagement
     */
    public function exportOrders(OrderVisitor $orderVisitor, ProgressBar $bar): bool
    {
        $eventDispatcher = $this->getEventDispatcher();
        $filesystem = $this->getFilesystem();
        $view = $this->getView();

        $bar->start(count($orderVisitor));

        foreach ($orderVisitor() as $num => $order) {
            set_time_limit(0);

            $bar->advance();

            $event = $eventDispatcher->dispatch(
                EventStore::PRE_ORDER_EXPORT,
                new PrepareOrderExportEvent($filesystem, $order)
            );

            $written = $filesystem->put(
                $file = $this->getOrderNameGenerator()->getOrderName($order),
                $view->render($this->getFileTemplate(), $event->getExportData())
            );

            $eventDispatcher->dispatch(
                EventStore::POST_ORDER_EXPORT,
                new FinishOrderExportEvent($file, $filesystem, $order)
            );
        }

        $bar->finish();

        return true;
    }

    /**
     * Returns the event dispatcher.
     * @return EventDispatcherInterface
     */
    private function getEventDispatcher(): EventDispatcherInterface
    {
        return $this->eventDispatcher;
    }

    /**
     * Returns the file system.
     * @return FilesystemInterface
     */
    private function getFilesystem(): FilesystemInterface
    {
        return $this->filesystem;
    }

    /**
     * Returns the template which should render the order.
     * @return string
     */
    private function getFileTemplate(): string
    {
        return $this->fileTemplate;
    }

    /**
     * Returns the generator for order names.
     * @return OrderNameGenerator
     */
    private function getOrderNameGenerator(): OrderNameGenerator
    {
        return $this->orderNameGenerator;
    }

    /**
     * Returns the view class.
     * @return Twig_Environment
     */
    private function getView(): Twig_Environment
    {
        return $this->view;
    }

    /**
     * Sets the used event dispatcher.
     * @param EventDispatcherInterface $eventDispatcher
     * @return Exporter
     */
    private function setEventDispatcher(EventDispatcherInterface $eventDispatcher): Exporter
    {
        $this->eventDispatcher = $eventDispatcher;

        return $this;
    }

    /**
     * Sets the file system.
     * @param FilesystemInterface $filesystem
     * @return Exporter
     */
    private function setFilesystem(FilesystemInterface $filesystem): Exporter
    {
        $this->filesystem = $filesystem;
        return $this;
    }

    /**
     * Sets the template which should render the order.
     * @param string $fileTemplate
     * @return Exporter
     */
    private function setFileTemplate(string $fileTemplate): Exporter
    {
        $this->fileTemplate = $fileTemplate;

        return $this;
    }

    /**
     * Sets the generator for order names.
     * @param OrderNameGenerator $orderNameGenerator
     * @return Exporter
     */
    private function setOrderNameGenerator(OrderNameGenerator $orderNameGenerator): Exporter
    {
        $this->orderNameGenerator = $orderNameGenerator;

        return $this;
    }

    /**
     * Sets the view class.
     * @param Twig_Environment $view
     * @return Exporter
     */
    private function setView(Twig_Environment $view): Exporter
    {
        $this->view = $view;

        return $this;
    }
}
