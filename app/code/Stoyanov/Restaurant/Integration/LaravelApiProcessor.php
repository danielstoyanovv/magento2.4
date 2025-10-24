<?php
declare(strict_types=1);

namespace Stoyanov\Restaurant\Integration;

use Magento\Framework\HTTP\Client\CurlFactory;
use Magento\Framework\HTTP\Client\Curl;
use Psr\Log\LoggerInterface;
use Stoyanov\Restaurant\Api\ApiProcessorInterface;
use Stoyanov\Restaurant\Helper\Data;

class LaravelApiProcessor implements ApiProcessorInterface
{
    /** @var string get restaurant's API link */
    private const string API_RESTAURANT_URL = "/api/restaurants";

    /** @var string get delete restaurant API link */
    private const string API_DELETE_URL = "/api/deleteRestaurant";

    /**
     * @param LoggerInterface $logger
     * @param CurlFactory $curlFactory
     * @param Data $data
     */
    public function __construct(
        private LoggerInterface $logger,
        private CurlFactory $curlFactory,
        private Data $data,
    ) {
    }

    /**
     * Create Restaurant
     *
     * @param array $data
     *
     * @return bool
     */
    public function createRestaurant(array $data): bool
    {
        try {
            $client = $this->getClient();
            $client->post(
                $this->getApiUrl() . self::API_RESTAURANT_URL,
                json_encode($this->prepareRestaurantData($data))
            );
            if ($client->getStatus() === 201) {
                $this->logger->info('The new Restaurant was created!');
                return true;
            }
            return false;
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }
        return false;
    }

    /**
     * Get Client
     *
     * @return Curl
     */
    public function getClient(): Curl
    {
        $curl = $this->curlFactory->create();
        $curl->addHeader("content-type", "application/json");
        $curl->addHeader("Accept", "application/json");
        $curl->addHeader(
            "authorization",
            "Bearer " . $this->getApiToken()
        );
        return $curl;
    }

    /**
     * Get Api Url
     *
     * @return mixed
     */
    private function getApiUrl(): mixed
    {
        return $this->data->getConfigValue("api_URL");
    }

    /**
     * Get Api Token
     *
     * @return mixed
     */
    private function getApiToken(): mixed
    {
        return $this->data->getConfigValue("api_token");
    }

    /**
     * Prepare Restaurant Data
     *
     * @param array $data
     *
     * @return array
     */
    private function prepareRestaurantData(array $data): array
    {
        $apiData = [];
        if (!empty($data['name'])) {
            $apiData['name'] = $data['name'];
        }
        if (!empty($data['location'])) {
            $apiData['location'] = $data['location'];
        }
        if (!empty($data['capacity'])) {
            $apiData['capacity'] = $data['capacity'];
        }
        if (!empty($data['magento_id'])) {
            $apiData['magento_id'] = $data['magento_id'];
        }
        return $apiData;
    }

    /**
     * Delete Restaurant
     *
     * @param int $id
     *
     * @return bool
     */

    public function deleteRestaurant(int $id): bool
    {
        try {
            $client = $this->getClient();
            $url = $this->getApiUrl() . self::API_DELETE_URL . '/' . $id;
            $client->setOption(CURLOPT_CUSTOMREQUEST, 'DELETE');

            $client->get($url);
            return true;

        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }
        return false;
    }
}
