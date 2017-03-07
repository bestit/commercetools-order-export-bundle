<?php

namespace BestIt\CtOrderExportBundle\Event;

use Exception;

/**
 * Event to mark the order-export as failed.
 * @author blange <lange@bestit-online.de>
 * @package BestIt\CtOrderExportBundle
 * @subpackage Event
 * @version $id$
 */
class FailedOrderExportEvent extends OrderExportEvent
{
    /**
     * A thrown exception.
     * @var void|Exception
     */
    private $exception = null;

    /**
     * Returns the exception if there is one.
     * @return Exception|void
     */
    public function getException()
    {
        return $this->exception;
    }

    /**
     * Sets the exception.
     * @param Exception $exception
     * @return FailedOrderExportEvent
     */
    public function setException(Exception $exception): FailedOrderExportEvent
    {
        $this->exception = $exception;
        
        return $this;
    }
}
