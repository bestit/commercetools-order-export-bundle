<?php

namespace BestIt\CtOrderExportBundle\Event;

use Commercetools\Core\Model\Order\Order;
use League\Flysystem\FilesystemInterface;

/**
 * Event to mark the order-export as finished.
 * @author blange <lange@bestit-online.de>
 * @package BestIt\CtOrderExportBundle
 * @subpackage Event
 * @version $id$
 */
class FinishOrderExportEvent extends OrderExportEvent
{
    /**
     * The exported file.
     * @var string
     */
    private $file = '';

    /**
     * FinishOrderExportEvent constructor.
     * @param string $file
     * @param FilesystemInterface $filesystem
     * @param Order $order
     */
    public function __construct(string $file, FilesystemInterface $filesystem, Order $order)
    {
        $this
            ->setFile($file);

        return parent::__construct($filesystem, $order);
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
     * Sets the file.
     * @param string $file
     * @return FinishOrderExportEvent
     */
    private function setFile(string $file): FinishOrderExportEvent
    {
        $this->file = $file;
        return $this;
    }
}
