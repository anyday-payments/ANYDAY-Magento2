<?php
declare(strict_types=1);

namespace Anyday\Payment\Block\Checkout\Cart;

use Anyday\Payment\Api\Data\Anydaytag\PricetagInterface;
use Anyday\Payment\Api\Data\Anydaytag\SettingsInterface;
use Anyday\Payment\Block\Adminhtml\Abstractpricetag;

class Pricetag extends Abstractpricetag implements PricetagInterface
{
    /**
     * @inheritdoc
     */
    public function getSerializedCartTagConfig()
    {
        $tagConfig = [];
        $tagConfig[PricetagInterface::NAME_IS_ENABLE]       = $this->isEnabled();
        $tagConfig[PricetagInterface::NAME_INLINE_CSS]      = $this->getInlineCss();
        $tagConfig[PricetagInterface::NAME_PRICE]           = $this->getPrice();
        $tagConfig[PricetagInterface::NAME_TAG_CODE]        = $this->getTagCode();
        $tagConfig[PricetagInterface::NAME_CURRENCY_CODE]   = $this->getCurrency();
        $tagConfig[PricetagInterface::NAME_NAME_SELECT_TAG] = $this->getNameSelectElement();

        return $this->jsonHexTagSerializer->serialize($tagConfig);
    }

    /**
     * @inheritdoc
     */
    public function isEnabled()
    {
        if ($this->config->isTagModuleEnable() && $this->isEnableProductPage()) {
            return true;
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function getSelectTagStyle()
    {
        return self::SELECT_TAG_STYLE;
    }

    /**
     * @inheritdoc
     */
    private function isEnableProductPage()
    {
        if ($this->config->getConfigValue(SettingsInterface::PATH_ENABLE_TAG_CART) == '1') {
            return true;
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function getInlineCss()
    {
        $inlineCss = $this->config->getConfigValue(SettingsInterface::PATH_TO_INLINECSS_CART);
        if ($inlineCss) {
            return $this->getInlineCssCode(
                $this->config->getConfigValue(SettingsInterface::PATH_TO_INLINECSS_CART),
                self::SELECT_TAG_STYLE
            );
        }

        return $inlineCss;
    }

    /**
     * @inheritdoc
     */
    public function getPrice()
    {
        return 0.0;
    }

    /**
     * @inheritdoc
     */
    public function getTagCode()
    {
        return $this->config->getTagToken();
    }

    /**
     * @inheritdoc
     */
    public function getCurrency()
    {
        return $this->config->getCurrencyCode();
    }

    /**
     * @inheritdoc
     */
    public function isViewFullPrice()
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function getNameSelectElement()
    {
        return $this->config->getConfigValue(SettingsInterface::PATH_TO_SELECT_TAG_ELEMENT_CART);
    }

    /**
     * @inheritdoc
     */
    public function getSerializedTagConfig()
    {
        return '';
    }
}
