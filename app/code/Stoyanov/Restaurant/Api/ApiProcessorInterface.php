<?php

namespace Stoyanov\Restaurant\Api;

use Magento\Framework\HTTP\Client\Curl;

interface ApiProcessorInterface
{
    /**
     * Create Restaurant
     *
     * @param array $data
     *
     * @return bool
     */
    public function createRestaurant(array $data): bool;

    /**
     * Get Client
     *
     * @return Curl
     */
    public function getClient(): Curl;

    /**
     * Delete Restaurant
     *
     * @param int $id
     *
     * @return bool
     */
    public function deleteRestaurant(int $id): bool;
}
