<?php

namespace BestIt\CtOrderExportBundle\Event;

use Commercetools\Core\Model\Order\Order;
use League\Flysystem\FilesystemInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Event to mark the order as prepared for export.
 * @author blange <lange@bestit-online.de>
 * @package BestIt\CtOrderExportBundle
 * @subpackage Event
 * @version $id$
 */
class PrepareOrderExportEvent extends Event
{
    /**
     * The export data.
     * @var array
     */
    private $exportData = [];

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
     * Adds an exportable data.
     * @param string|array $key
     * @param null $data
     * @return PrepareOrderExportEvent
     */
    public function addExportData($key, $data = null): PrepareOrderExportEvent
    {
        if (is_array($key)) {
            $this->exportData = array_merge($this->exportData, $key);
        } else {
            $this->exportData[$key] = $data;
        }

        return $this;
    }

    /**
     * Returns the exportable data.
     * @return array
     */
    public function getExportData(): array
    {
        return $this->exportData + ['order' => $this->getOrder()];
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
     * @return PrepareOrderExportEvent
     */
    private function setFilesystem(FilesystemInterface $filesystem): PrepareOrderExportEvent
    {
        $this->filesystem = $filesystem;
        return $this;
    }

    /**
     * Sets the used order.
     * @param Order $order
     * @return PrepareOrderExportEvent
     */
    private function setOrder(Order $order): PrepareOrderExportEvent
    {
        $this->order = $order;

        return $this;
    }
}
