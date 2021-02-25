<?php
declare(strict_types=1);

namespace Anyday\PaymentAndTrack\Api\Settings;

interface AnydayendpointInterface
{
    /**
     * Get Credaintials to Endpoint
     *
     * @return string
     */
    public function getValidateCredentialsEndpoint();
}
