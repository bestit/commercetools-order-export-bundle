<?php

namespace BestIt\CtOrderExportBundle;

use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Factory for a progress bar.
 * @author blange <lange@bestit-online.de>
 * @package BestIt\CtOrderExportBundle
 * @version $id$
 */
class ProgressBarFactory
{
    /**
     * Returns a progress bar.
     * @param OutputInterface $output
     * @param int $max
     * @return ProgressBar
     */
    public function getProgressBar(OutputInterface $output, $max = 0): ProgressBar
    {
        return new ProgressBar($output, $max);
    }
}
