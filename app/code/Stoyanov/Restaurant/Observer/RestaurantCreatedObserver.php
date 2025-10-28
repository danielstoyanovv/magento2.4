<?php
declare(strict_types=1);

namespace Stoyanov\Restaurant\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface;
use Stoyanov\Restaurant\Api\ApiProcessorInterface;

class RestaurantCreatedObserver implements ObserverInterface
{
    /**
     * @param LoggerInterface $logger
     * @param ApiProcessorInterface $apiProcessor
     */
    public function __construct(
        private LoggerInterface $logger,
        private ApiProcessorInterface $apiProcessor
    ) {
    }

    /**
     * Execute action
     *
     * @param Observer $observer
     *
     * @return void
     */
    public function execute(Observer $observer): void
    {
        // Get data from event
        $restaurant = $observer->getData('restaurant');

        // Log data
        $this->logger->info(
            '
        New restaurant created: ' . $restaurant['name'] . ' | Capacity: ' .
            $restaurant['capacity'] . ' | Location: ' . $restaurant['location']
        );
        $this->apiProcessor->createRestaurant([
            'name' => $restaurant['name'],
            'capacity' => $restaurant['capacity'],
            'location' => $restaurant['location'],
            'magento_id' => $restaurant['entity_id'],
        ]);
    }
}
