<?php
declare(strict_types=1);

namespace Anyday\Payment\Api\Anyday;

interface ManagerInterface
{
    const ANYDAY_BASE_URL = 'https://my.anyday.io/api/';
    const ANYDAY_AUTH_URL = 'v1/authentication/login';

    /**
     * Get Credentials
     *
     * @param string $data
     * @return mixed
     */
    public function getCredentialsFromAnyday(string $data);
}
