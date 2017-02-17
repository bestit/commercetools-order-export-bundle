<?php

namespace BestIt\CtOrderExportBundle;

use Commercetools\Core\Client;
use Commercetools\Core\Model\Order\OrderCollection;
use Commercetools\Core\Request\Orders\OrderQueryRequest;
use Commercetools\Core\Response\ErrorResponse;
use Commercetools\Core\Response\PagedQueryResponse;
use Countable;
use Generator;
use RuntimeException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Iterates over the complete found list.
 * @author blange <lange@bestit-online.de>
 * @package BestIt\CtOrderExportBundle
 * @version $id$
 */
class OrderVisitor implements Countable
{
    /**
     * The used client.
     * @var Client
     */
    private $client = null;

    /**
     * The default where for the order query.
     * @var array
     */
    private $defaultWhere = [];

    /**
     * The used event dispatcher.
     * @var EventDispatcherInterface
     */
    private $eventDispatcher = null;

    /**
     * The last response for the fetching of orders.
     * @var PagedQueryResponse
     */
    protected $lastResponse = null;

    /**
     * The found order collection.
     * @var void|OrderCollection
     */
    protected $orderCollection = null;

    /**
     * The query to fetch the orders.
     * @var OrderQueryRequest|void
     */
    protected $orderQuery = null;

    /**
     * The total count of found orders.
     * @var int
     */
    protected $totalCount = -1;

    /**
     * Is a pagination used?
     * @var bool
     */
    private $withPagination = true;

    /**
     * OrderVisitor constructor.
     * @param Client $client
     * @param EventDispatcherInterface $eventDispatcher
     * @param bool $withPagination
     */
    public function __construct(Client $client, EventDispatcherInterface $eventDispatcher, bool $withPagination = true)
    {
        $this
            ->setClient($client)
            ->setEventDispatcher($eventDispatcher)
            ->withPagination($withPagination);
    }

    /**
     * Yields the orders.
     * @return Generator
     */
    public function __invoke()
    {
        return $this->yieldOrders();
    }

    /**
     * Count elements of an object
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
     * @since 5.1.0
     */
    public function count()
    {
        return $this->getTotalCount();
    }

    /**
     * Returns the used client.
     * @return Client
     */
    private function getClient(): Client
    {
        return $this->client;
    }

    /**
     * Returns the default where clause for the order query.
     * @return array
     */
    public function getDefaultWhere(): array
    {
        return $this->defaultWhere;
    }

    /**
     * Returns the event dispatcher.
     * @return EventDispatcherInterface
     */
    private function getEventDispatcher(): EventDispatcherInterface
    {
        return $this->eventDispatcher;
    }

    /**
     * Returns the last order fetching response.
     * @return PagedQueryResponse
     */
    private function getLastResponse(): PagedQueryResponse
    {
        if (!$this->lastResponse) {
            $this->loadOrderCollection();
        }

        return $this->lastResponse;
    }

    /**
     * Returns the next order collection or void.
     * @param int $renderedArticleCount How many articles are rendered allready.
     * @return OrderCollection|void
     */
    private function getNextOrderCollection(int $renderedArticleCount)
    {
        $orderCollection = null;

        if ((!$withPagination = $this->withPagination()) || ($renderedArticleCount < $this->count())) {
            if ($withPagination) {
                $this->getOrderQuery()->offset($renderedArticleCount);
            }

            $this->loadOrderCollection();

            $orderCollection = $this->getOrderCollection();
        }

        return $orderCollection;
    }

    /**
     * Returns the found order collection.
     * @return OrderCollection
     */
    private function getOrderCollection(): OrderCollection
    {
        if (!$this->orderCollection) {
            $this->loadOrderCollection();
        }

        return $this->orderCollection;
    }

    /**
     * Returns the order query.
     * @return OrderQueryRequest
     */
    private function getOrderQuery(): OrderQueryRequest
    {
        if (!$this->orderQuery) {
            $this->loadOrderQuery();
        }

        return $this->orderQuery;
    }

    /**
     * Returns the total count of found orders.
     * @return int
     */
    private function getTotalCount(): int
    {
        if ($this->totalCount === -1) {
            $this->setTotalCount($this->getLastResponse()->getTotal());
        }

        return $this->totalCount;
    }

    /**
     * Loads the order collection.
     * @return OrderVisitor
     */
    private function loadOrderCollection(): OrderVisitor
    {
        $eventDispatcher = $this->getEventDispatcher();

        $response = $this->getClient()->execute($this->getOrderQuery());

        if ($response instanceof ErrorResponse) {
            throw new RuntimeException($response->getMessage());
        }

        // TODO Add some events.
        $this->setLastResponse($response);

        $this->setOrderCollection($this->getLastResponse()->toObject());

        return $this;
    }

    /**
     * Returns the order query request.
     * @return OrderVisitor
     */
    private function loadOrderQuery(): OrderVisitor
    {
        $query = new OrderQueryRequest();

        if ($wheres = $this->getDefaultWhere()) {
            array_walk($wheres, function (string $where) use ($query) {
                $query->where($where);
            });
        }

        $this->setOrderQuery($query);

        return $this;
    }

    /**
     * Sets the used client.
     * @param Client $client
     * @return OrderVisitor
     */
    private function setClient(Client $client): OrderVisitor
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Sets the default where clause for the order query.
     * @param array $defaultWhere
     * @return OrderVisitor
     */
    public function setDefaultWhere(array $defaultWhere): OrderVisitor
    {
        $this->defaultWhere = $defaultWhere;

        return $this;
    }

    /**
     * Sets the used event dispatcher.
     * @param EventDispatcherInterface $eventDispatcher
     * @return OrderVisitor
     */
    private function setEventDispatcher(EventDispatcherInterface $eventDispatcher): OrderVisitor
    {
        $this->eventDispatcher = $eventDispatcher;

        return $this;
    }

    /**
     * Sets the last order fetching response.
     * @param PagedQueryResponse $lastResponse
     * @return OrderVisitor
     */
    private function setLastResponse(PagedQueryResponse $lastResponse): OrderVisitor
    {
        $this->lastResponse = $lastResponse;

        return $this;
    }

    /**
     * Sets the order collection.
     * @param OrderCollection $orderCollection
     * @return OrderVisitor
     */
    private function setOrderCollection(OrderCollection $orderCollection): OrderVisitor
    {
        $this->orderCollection = $orderCollection;

        return $this;
    }

    /**
     * Sets the order query.
     * @param OrderQueryRequest $orderQuery
     * @return OrderVisitor
     */
    private function setOrderQuery(OrderQueryRequest $orderQuery): OrderVisitor
    {
        $this->orderQuery = $orderQuery;

        return $this;
    }

    /**
     * Sets the total count of found orders.
     * @param int $totalCount
     * @return OrderVisitor
     */
    private function setTotalCount(int $totalCount): OrderVisitor
    {
        $this->totalCount = $totalCount;

        return $this;
    }

    /**
     * Sets the pagination status for this list.
     * @param bool $newStatus
     * @return bool
     */
    private function withPagination(bool $newStatus = true): bool
    {
        $oldStatus = $this->withPagination;

        if (func_num_args()) {
            $this->withPagination = $newStatus;
        }

        return $oldStatus;
    }

    /**
     * Yields all found orders.
     * @return Generator
     */
    public function yieldOrders()
    {
        $usedIndex = 0;
        $orderCollection = $this->getOrderCollection();

        while ($orderCollection && count($orderCollection)) {
            foreach ($orderCollection as $order) {
                set_time_limit(0);

                yield $usedIndex++ => $order;
            }

            $orderCollection = $this->getNextOrderCollection($usedIndex);
        }
    }
}
