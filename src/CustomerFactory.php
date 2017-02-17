<?php

namespace BestIt\CtOrderExportBundle;

use Commercetools\Core\Client;
use Commercetools\Core\Model\Customer\Customer;
use Commercetools\Core\Request\Customers\CustomerByIdGetRequest;
use Commercetools\Core\Response\ErrorResponse;
use InvalidArgumentException;
use Symfony\Component\Cache\Adapter\AdapterInterface;

/**
 * Loads customers.
 * @author blange <lange@bestit-online.de>
 * @package BestIt\CtOrderExportBundle
 * @version $id$
 */
class CustomerFactory
{
    /**
     * The used cache class.s
     * @var AdapterInterface
     */
    private $cache = null;

    /**
     * The used web service client.
     * @var Client
     */
    private $client = null;

    /**
     * CustomerFactory constructor.
     * @param AdapterInterface $cache
     * @param Client $client
     */
    public function __construct(AdapterInterface $cache, Client $client)
    {
        $this
            ->setCache($cache)
            ->setClient($client);
    }

    /**
     * Returns the cache.
     * @return AdapterInterface
     */
    private function getCache(): AdapterInterface
    {
        return $this->cache;
    }

    /**
     * Returns the client.
     * @return Client
     */
    private function getClient(): Client
    {
        return $this->client;
    }

    /**
     * Returns the customer object matching the customer id.
     * @param string $customerId
     * @return Customer
     */
    public function getCustomer(string $customerId): Customer
    {
        $cache = $this->getCache();
        $cacheHit = $cache->getItem($customerId);

        if (!$cacheHit->isHit()) {
            $response = $this->getClient()->execute(CustomerByIdGetRequest::ofId($customerId));

            if ($response instanceof ErrorResponse) {
                throw new InvalidArgumentException(sprintf(
                   'Can not load the customer with the given id %s.',
                    $customerId
                ));
            }

            $cacheHit
                ->expiresAfter(3600)
                ->set($response->toArray());

            $this->getCache()->save($cacheHit);
        }

        return Customer::fromArray($cacheHit->get());
    }

    /**
     * Sets the cache.
     * @param AdapterInterface $cache
     * @return CustomerFactory
     */
    private function setCache(AdapterInterface $cache): CustomerFactory
    {
        $this->cache = $cache;
        return $this;
    }

    /**
     * Sets the client.
     * @param Client $client
     * @return CustomerFactory
     */
    private function setClient(Client $client): CustomerFactory
    {
        $this->client = $client;
        return $this;
    }
}
