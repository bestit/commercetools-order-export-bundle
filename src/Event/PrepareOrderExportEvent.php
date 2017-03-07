<?php

namespace BestIt\CtOrderExportBundle\Event;

/**
 * Event to mark the order as prepared for export.
 * @author blange <lange@bestit-online.de>
 * @package BestIt\CtOrderExportBundle
 * @subpackage Event
 * @version $id$
 */
class PrepareOrderExportEvent extends OrderExportEvent
{
    /**
     * The export data.
     * @var array
     */
    private $exportData = [];

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
}
