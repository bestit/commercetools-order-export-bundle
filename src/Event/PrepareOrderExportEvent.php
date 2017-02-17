<?php

namespace BestIt\CtOrderExportBundle\Event;

use Commercetools\Core\Model\Order\Order;
use Symfony\Component\EventDispatcher\Event;

/**
 * Event to mark the order as prepared for export.
 * @author blange <lange@bestit-online.de>
 * @package BestIt\CtOrderExportBundle\Event
 * @version $id$
 */
class PrepareOrderExportEvent extends Event
{
    /**
     * The exportable order.
     * @var Order
     */
    private $order = null;

    /**
     * PrepareOrderExportEvent constructor.
     * @param Order $order
     */
    public function __construct(Order $order)
    {
        $this->setOrder($order);
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
