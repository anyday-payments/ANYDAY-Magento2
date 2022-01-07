<?php
declare(strict_types=1);

namespace Anyday\Payment\Api\Data\Payment;

interface UrlDataInterface
{
    const URL_ANYDAY            = 'https://my.anyday.io/';
    const URL_AUTORIZE          = 'https://my.anyday.io/v1/payments';
    const URL_CAPTURE           = '/v1/payments/{id}/capture';
    const URL_ANYDAY_PAYMENT    = 'https://my.anyday.io';
    const URL_REFUND            = '/v1/payments/{id}/refund';
    const URL_CANCEL            = '/v1/payments/{id}/cancel';

    /**
     * Get Autorize Url
     *
     * @return string
     */
    public function getAutorizeUrl(): string;

    /**
     * Get Capture Url
     *
     * @param string $idPayment
     * @return string
     */
    public function getCaptureUrl(string $idPayment): string;

    /**
     * Get Refund Url
     *
     * @param string $idPayment
     * @return string
     */
    public function getRefundUrl(string $idPayment): string;
}
