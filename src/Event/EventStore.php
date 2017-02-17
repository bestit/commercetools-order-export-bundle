<?php

namespace BestIt\CtOrderExportBundle\Event;

/**
 * Saving the event names for this bundle.
 * @author blange <lange@bestit-online.de>
 * @package BestIt\CtOrderExportBundle
 * @subpackage Event
 * @version $id$
 */
final class EventStore
{
    /**
     * Is triggered after the export of an order.
     * @var string
     */
    const POST_ORDER_EXPORT = 'order_export.postOrderExport';

    /**
     * Is triggered after the export of an order.
     * @var string
     */
    const PRE_ORDER_EXPORT = 'order_export.preOrderExport';
}
