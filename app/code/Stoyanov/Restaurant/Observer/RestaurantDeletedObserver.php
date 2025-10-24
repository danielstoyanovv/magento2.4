<?php
declare(strict_types=1);

namespace Stoyanov\Restaurant\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface;
use Stoyanov\Restaurant\Api\ApiProcessorInterface;

class RestaurantDeletedObserver implements ObserverInterface
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
        $restaurantId = $observer->getData('restaurant_id');
        // Log data
        $this->logger->info('Restaurant was deleted: '  . $restaurantId);
        $this->apiProcessor->deleteRestaurant($restaurantId);
    }
}
