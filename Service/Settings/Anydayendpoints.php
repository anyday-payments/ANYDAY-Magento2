<?php
declare(strict_types=1);

namespace Anyday\Payment\Service\Settings;

use Anyday\Payment\Api\Settings\AnydayendpointInterface;
use Magento\Framework\UrlInterface;

class Anydayendpoints implements AnydayendpointInterface
{
    /**
     * @var UrlInterface
     */
    private $urlInterface;

    /**
     * Anydayendpoints constructor.
     *
     * @param UrlInterface $urlInterface
     */
    public function __construct(
        UrlInterface $urlInterface
    ) {
        $this->urlInterface = $urlInterface;
    }

    /**
     * Return validate Url
     *
     * @return string
     */
    public function getValidateCredentialsEndpoint()
    {
        return $this->urlInterface->getBaseUrl() . 'rest/V1/validate/credentials/';
    }
}
