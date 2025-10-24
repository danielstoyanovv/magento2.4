<?php
declare(strict_types=1);

namespace Stoyanov\Restaurant\Integration;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\HTTP\Client\CurlFactory;
use Psr\Log\LoggerInterface;
use Stoyanov\Restaurant\Api\AccountManagerInterface;
use Stoyanov\Restaurant\Helper\Data;

class AccountManager implements AccountManagerInterface
{
    /** @var string get user register form link */
    public const string API_REGISTER_URL = "/api/register";

    /** @var string get user login form link */
    public const string API_LOGIN_URL = "/api/login";

    /**
     * @param LoggerInterface $logger
     * @param CurlFactory $curlFactory
     * @param Data $data
     * @param WriterInterface $configWriter
     */
    public function __construct(
        private LoggerInterface $logger,
        private CurlFactory $curlFactory,
        private Data $data,
        private WriterInterface $configWriter
    ) {
    }

    /**
     * Get Client
     *
     * @param bool $useToken
     * @param $token
     *
     * @return Curl
     */
    public function getClient(): Curl
    {
        $curl = $this->curlFactory->create();
        $curl->addHeader("content-type", "application/json");
        $curl->addHeader("Accept", "application/json");
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
     * Prepare Register Data
     *
     * @param array $data
     *
     * @return array
     */
    private function prepareRegisterData(array $data): array
    {
        $apiData = [];
        if (!empty($data['name'])) {
            $apiData['name'] = $data['name'];
        }
        if (!empty($data['email'])) {
            $apiData['email'] = $data['email'];
        }
        if (!empty($data['password'])) {
            $apiData['password'] = $data['password'];
            $apiData['password_confirmation'] = $data['password'];
        }
        return $apiData;
    }

    /**
     * Prepare Login Data
     *
     * @param array $data
     *
     * @return array
     */
    private function prepareLoginData(array $data): array
    {
        $apiData = [];
        if (!empty($data['email'])) {
            $apiData['email'] = $data['email'];
        }
        if (!empty($data['password'])) {
            $apiData['password'] = $data['password'];
        }
        return $apiData;
    }
    /**
     * Login Profile
     *
     * @param array $data
     * @return bool
     */
    public function loginProfile(array $data): bool
    {
        try {
            $client = $this->getClient();
            $client->post(
                $this->getApiUrl() . self::API_LOGIN_URL,
                json_encode($this->prepareLoginData($data))
            );
            if ($client->getStatus() === 200) {
                $response = json_decode($client->getBody(), true);
                $token = $response['token'];
                $this->configWriter->save(
                    'restaurants_settings/general/api_token',
                    $token,
                    ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
                    0
                );
                return true;
            }
            return false;
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }
        return false;
    }

    /**
     * Create Profile
     *
     * @param array $data
     *
     * @return bool
     */
    public function createProfile(array $data): bool
    {
        try {
            $client = $this->getClient();
            $client->post($this->getApiUrl() . self::API_REGISTER_URL, json_encode($this->prepareRegisterData($data)));
            if ($client->getStatus() === 201) {
                $this->logger->info('The new API profile was created!');
                return true;
            }
            return false;
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }
        return false;
    }
}
