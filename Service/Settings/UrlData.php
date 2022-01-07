<?php
declare(strict_types=1);

namespace Anyday\Payment\Service\Settings;

use Anyday\Payment\Api\Data\Payment\UrlDataInterface;

class UrlData implements UrlDataInterface
{
    /**
     * @inheritDoc
     */
    public function getAutorizeUrl(): string
    {
        return self::URL_ANYDAY . self::URL_AUTORIZE;
    }

    /**
     * @inheritDoc
     */
    public function getCaptureUrl(string $idPayment): string
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function getRefundUrl(string $idPayment): string
    {
        return '';
    }
}
