<?php

namespace BestIt\CtOrderExportBundle\Event;

use Commercetools\Core\Model\Order\Order;
use League\Flysystem\FilesystemInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Basic event for the order export.
 * @author blange <lange@bestit-online.de>
 * @package BestIt\CtOrderExportBundle
 * @subpackage Event
 * @version $id$
 */
class OrderExportEvent extends Event
{
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
     * PrepareOrderExportEvent constructor.
     * @param FilesystemInterface $filesystem
     * @param Order $order
     */
    public function __construct(FilesystemInterface $filesystem, Order $order)
    {
        $this
            ->setFilesystem($filesystem)
            ->setOrder($order);
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
     * Sets the file system.
     * @param FilesystemInterface $filesystem
     * @return OrderExportEvent
     */
    private function setFilesystem(FilesystemInterface $filesystem): OrderExportEvent
    {
        $this->filesystem = $filesystem;
        return $this;
    }

    /**
     * Sets the used order.
     * @param Order $order
     * @return OrderExportEvent
     */
    private function setOrder(Order $order): OrderExportEvent
    {
        $this->order = $order;

        return $this;
    }
}
