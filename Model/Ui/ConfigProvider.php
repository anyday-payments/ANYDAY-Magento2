<?php
declare(strict_types=1);

namespace Anyday\PaymentAndTrack\Model\Ui;

use Anyday\PaymentAndTrack\Service\Settings\Config;
use Magento\Checkout\Model\ConfigProviderInterface;

class ConfigProvider implements ConfigProviderInterface
{
    const CODE = 'anyday';

    /**
     * @var Config
     */
    private $configAnyday;

    /**
     * ConfigProvider constructor.
     *
     * @param Config $configAnyday
     */
    public function __construct(
        Config $configAnyday
    ) {
        $this->configAnyday = $configAnyday;
    }

    /**
     * Retrieve assoc array of checkout configuration
     *
     * @return array
     */
    public function getConfig(): array
    {
        return [
            'payment' => [
                self::CODE => [
                    'isActive' => (bool)(int)$this->configAnyday->getConfigValue(
                        Config::PATH_ENABLE_PAYMENT_MODULE
                    )
                ]
            ]
        ];
    }
}
