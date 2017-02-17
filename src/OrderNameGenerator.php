<?php

namespace BestIt\CtOrderExportBundle;

use Commercetools\Core\Model\Order\Order;

/**
 * Generates the name of the order file.
 * @author blange ylange@bestit-online.de>
 * @package BestIt\CtOrderExportBundle
 * @version $id$
 */
class OrderNameGenerator
{
    /**
     * The name scheme.
     * @var string
     */
    private $nameScheme = '';

    /**
     * OrderNameGenerator constructor.
     * @param string $nameScheme
     */
    public function __construct(string $nameScheme)
    {
        $this->setNameScheme($nameScheme);
    }

    /**
     * Returns the name scheme.
     * @return string
     */
    private function getNameScheme(): string
    {
        return $this->nameScheme;
    }

    /**
     * Returns the name for the order.
     * @param Order $order
     * @return string
     */
    public function getOrderName(Order $order): string
    {
        if (preg_match_all('/{{(.*)}}/U', $scheme = $this->getNameScheme(), $matches)) {
            foreach ($matches[1] as $index => $foundSnippet) {
                $foundSnippetName = trim($foundSnippet);
                $usedSnippet = $order->hasField($foundSnippetName)
                    ? $order->get($foundSnippetName)
                    : date($foundSnippetName);

                $scheme = str_replace($matches[0][$index], $usedSnippet, $scheme);
            }
        }

        return $scheme;
    }

    /**
     * Sets the name scheme.
     * @param string $nameScheme
     * @return OrderNameGenerator
     */
    private function setNameScheme(string $nameScheme): OrderNameGenerator
    {
        $this->nameScheme = $nameScheme;
        return $this;
    }
}
