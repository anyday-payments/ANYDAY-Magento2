<?php
declare(strict_types=1);

namespace Anyday\PaymentAndTrack\Api\Data\Andytag;

interface PaymentLogoInterface
{
    const NAME_LOGO_URL         = 'logo_url';
    const NAME_IS_VISIBLE_LOGO  = 'is_visible_logo';

    /**
     * Get Logo Url
     *
     * @return string
     */
    public function getLogoUrl(): string;
}
