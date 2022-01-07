<?php
declare(strict_types=1);

namespace Anyday\Payment\Api\Payment;

interface AnydayUrlInterface
{
    /**
     * Get Anyday Payment Url
     *
     * @param string $cartId
     * @return string
     */
    public function getAnydayPaymentUrl(string $cartId): string;
}
