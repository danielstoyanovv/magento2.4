<?php

namespace Stoyanov\Restaurant\Api;

use Magento\Framework\HTTP\Client\Curl;

interface AccountManagerInterface
{
    /**
     * Get Client
     *
     * @param bool $useToken
     * @param $token
     *
     * @return Curl
     */
    public function getClient(): Curl;

    /**
     * Login Profile
     *
     * @param array $data
     * @return bool
     */
    public function loginProfile(array $data): bool;

    /**
     * Create Profile
     *
     * @param array $data
     *
     * @return bool
     */
    public function createProfile(array $data): bool;
}
