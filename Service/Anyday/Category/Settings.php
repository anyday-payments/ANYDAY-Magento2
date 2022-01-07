<?php
declare(strict_types=1);

namespace Anyday\Payment\Service\Anyday\Category;

use Anyday\Payment\Api\Data\Andytag\SettingsInterface;
use Anyday\Payment\Service\Settings\Config;
use Magento\Framework\Exception\NoSuchEntityException;

class Settings implements SettingsInterface
{
    /**
     * @var Config
     */
    private $config;

    /**
     * Settings constructor.
     *
     * @param Config $config
     */
    public function __construct(
        Config $config
    ) {
        $this->config   = $config;
    }

    /**
     * Get is enable
     *
     * @return bool
     */
    public function isEnable()
    {
        if ($this->config->isTagModuleEnable() && $this->isEnableCategoryPage()) {
            return true;
        }

        return false;
    }

    /**
     * Get pricetag html
     *
     * @param float $price
     * @return string
     * @throws NoSuchEntityException
     */
    public function getPriceTagHtml($price)
    {
        $ret = '';
        if ($this->isEnable()) {
            $ret = '<div class="extra-info" ' . $this->getInlineCss() . '>
                <anyday-price-tag style="display: block;"
                                  total-price="' . $price . '"
                                  price-tag-token="' . $this->config->getTagToken() . ' "
                                  currency="' . $this->config->getCurrencyCode() . '"
                                   environment="production" type="module">
                </anyday-price-tag>
            </div>';
        }

        return $ret;
    }

    /**
     * Get is Enable in category page
     *
     * @return bool
     */
    private function isEnableCategoryPage()
    {
        if ($this->config->getConfigValue(self::PATH_ENABLE_TAG_CATEGORY) == '1') {
            return true;
        }

        return false;
    }

    /**
     * Get inline css
     *
     * @return string
     */
    private function getInlineCss()
    {
        $style  = '';
        if ($inlineCss = $this->config->getConfigValue(self::PATH_TO_INLINECSS_CATEGORY)) {
            $style = "style=\"$inlineCss\"";
        }

        return $style;
    }
}
