<?php

namespace BestIt\CtOrderExportBundle\Event;

use Commercetools\Core\Model\Order\Order;
use League\Flysystem\FilesystemInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Event to mark the order-export as finished.
 * @author blange <lange@bestit-online.de>
 * @package BestIt\CtOrderExportBundle
 * @subpackage Event
 * @version $id$
 */
class FinishOrderExportEvent extends Event
{
    /**
     * The exported file.
     * @var string
     */
    private $file = '';

    /**
     * The used file system.
     * @var FilesystemInterface
     */
    private $filesystem = null;

    /**
     * The exportable order.
     * @var Order
     */
    private $order = null;

    /**
     * FinishOrderExportEvent constructor.
     * @param string $file
     * @param FilesystemInterface $filesystem
     * @param Order $order
     */
    public function __construct(string $file, FilesystemInterface $filesystem, Order $order)
    {
        $this
            ->setFile($file)
            ->setFilesystem($filesystem)
            ->setOrder($order);
    }

    /**
     * Returns the file.
     * @return string
     */
    public function getFile(): string
    {
        return $this->file;
    }

    /**
     * Returns the file system.
     * @return FilesystemInterface
     */
    public function getFilesystem(): FilesystemInterface
    {
        return $this->filesystem;
    }

    /**
     * Returns the used order.
     * @return Order
     */
    public function getOrder(): Order
    {
        return $this->order;
    }

    /**
     * Sets the file.
     * @param string $file
     * @return FinishOrderExportEvent
     */
    private function setFile(string $file): FinishOrderExportEvent
    {
        $this->file = $file;
        return $this;
    }

    /**
     * Sets the file system.
     * @param FilesystemInterface $filesystem
     * @return FinishOrderExportEvent
     */
    private function setFilesystem(FilesystemInterface $filesystem): FinishOrderExportEvent
    {
        $this->filesystem = $filesystem;
        return $this;
    }

    /**
     * Sets the used order.
     * @param Order $order
     * @return FinishOrderExportEvent
     */
    private function setOrder(Order $order): FinishOrderExportEvent
    {
        $this->order = $order;

        return $this;
    }
}
