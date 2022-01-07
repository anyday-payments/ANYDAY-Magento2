<?php
declare(strict_types=1);

namespace Anyday\Payment\Api\Settings;

interface AnydayendpointInterface
{
    /**
     * Get Credaintials to Endpoint
     *
     * @return string
     */
    public function getValidateCredentialsEndpoint();
}
