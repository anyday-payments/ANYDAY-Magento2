<?php
declare(strict_types=1);

namespace Anyday\PaymentAndTrack\Block\Checkout\Onestep;

use Anyday\PaymentAndTrack\Api\Data\Andytag\PaymentLogoInterface;
use Anyday\PaymentAndTrack\Api\Data\Andytag\PricetagInterface;
use Anyday\PaymentAndTrack\Api\Data\Andytag\SettingsInterface;
use Anyday\PaymentAndTrack\Block\Adminhtml\Abstractpricetag;

class Pricetag extends Abstractpricetag implements PricetagInterface, PaymentLogoInterface
{
    /**
     * @inheritdoc
     */
    public function getSerializedCartTagConfig()
    {
        $tagConfig = [];
        $tagConfig[PricetagInterface::NAME_IS_ENABLE]                    = $this->isEnabled();
        $tagConfig[PricetagInterface::NAME_IS_PAYMENT_METHOD_TAG_ENABLE] = $this->isPaymentMethodTagEnabled();
        $tagConfig[PricetagInterface::NAME_INLINE_CSS]                   = $this->getInlineCss();
        $tagConfig[PricetagInterface::NAME_PAYMENT_INLINE_CSS]           = $this->getPaymentInlineCss();
        $tagConfig[PricetagInterface::NAME_PRICE]                        = $this->getPrice();
        $tagConfig[PricetagInterface::NAME_TAG_CODE]                     = $this->getTagCode();
        $tagConfig[PricetagInterface::NAME_CURRENCY_CODE]                = $this->getCurrency();
        $tagConfig[PaymentLogoInterface::NAME_LOGO_URL]                  = $this->getLogoUrl();

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
    public function isPaymentMethodTagEnabled()
    {
        if ($this->config->isTagModuleEnable() &&
            $this->config->getConfigValue(SettingsInterface::PATH_ENABLE_PAYMENT_METHOD_TAG_CHECKOUT) == '1') {
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
        if ($this->config->getConfigValue(SettingsInterface::PATH_ENABLE_TAG_CHECKOUT) == '1') {
            return true;
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function getInlineCss()
    {
        $inlineCss = $this->config->getConfigValue(SettingsInterface::PATH_TO_INLINECSS_CHECKOUT);
        if ($inlineCss) {
            return $this->getInlineCssCode(
                $this->config->getConfigValue(SettingsInterface::PATH_TO_INLINECSS_CHECKOUT),
                self::SELECT_TAG_STYLE
            );
        }

        return $inlineCss;
    }

    /**
     * @inheritdoc
     */
    public function getPaymentInlineCss()
    {
        $inlineCss = $this->config->getConfigValue(SettingsInterface::PATH_TO_INLINECSS_CHECKOUT_PAYMENT);
        if ($inlineCss) {
            return $this->getInlineCssCode(
                $this->config->getConfigValue(SettingsInterface::PATH_TO_INLINECSS_CHECKOUT_PAYMENT),
                self::SELECT_PAYMENT_METHOD_TAG_STYLE
            );
        }

        return $inlineCss;
    }

    /**
     * @inheritdoc
     */
    public function getPrice()
    {
        return (float)0.0;
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
    public function getSerializedTagConfig()
    {
        return '';
    }

    /**
     * @inheritdoc
     */
    public function getNameSelectElement()
    {
        return '';
    }

    /**
     * @inheritdoc
     */
    public function getLogoUrl(): string
    {
        return $this->getViewFileUrl('Anyday_PaymentAndTrack/images/ANYDAY_Logo.svg');
    }
}
