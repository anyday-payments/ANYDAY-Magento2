<?php
declare(strict_types=1);

namespace Anyday\PaymentAndTrack\Block\Catalog\Product;

use Anyday\PaymentAndTrack\Api\Data\Andytag\PricetagInterface;
use Anyday\PaymentAndTrack\Api\Data\Andytag\SettingsInterface;
use Anyday\PaymentAndTrack\Service\Settings\Config;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Block\Product\View;
use Magento\Catalog\Helper\Product;
use Magento\Catalog\Model\ProductTypes\ConfigInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\Locale\FormatInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\Stdlib\StringUtils;
use Magento\Framework\Url\EncoderInterface;

class Pricetag extends View implements PricetagInterface
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var JsonHexTag
     */
    private $jsonHexTagSerializer;

    /**
     * @param Context $context
     * @param EncoderInterface $urlEncoder
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param StringUtils $string
     * @param Product $productHelper
     * @param ConfigInterface $productTypeConfig
     * @param FormatInterface $localeFormat
     * @param Session $customerSession
     * @param ProductRepositoryInterface $productRepository
     * @param PriceCurrencyInterface $priceCurrency
     * @param Config $config
     * @param Json $jsonHexTagSerializer
     * @param array $data
     */
    public function __construct(
        Context $context,
        EncoderInterface $urlEncoder,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        StringUtils $string,
        Product $productHelper,
        ConfigInterface $productTypeConfig,
        FormatInterface $localeFormat,
        Session $customerSession,
        ProductRepositoryInterface $productRepository,
        PriceCurrencyInterface $priceCurrency,
        Config $config,
        Json $jsonHexTagSerializer,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $urlEncoder,
            $jsonEncoder,
            $string,
            $productHelper,
            $productTypeConfig,
            $localeFormat,
            $customerSession,
            $productRepository,
            $priceCurrency,
            $data
        );

        $this->config               = $config;
        $this->jsonHexTagSerializer = $jsonHexTagSerializer;
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
    private function isEnableProductPage()
    {
        if ($this->config->getConfigValue(SettingsInterface::PATH_ENABLE_TAG_PRODUCT) == '1') {
            return true;
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function getInlineCss()
    {
        return $this->config->getConfigValue(SettingsInterface::PATH_TO_INLINECSS_PRODUCT);
    }

    /**
     * @inheritdoc
     */
    public function getPrice()
    {
        if ($this->getProduct()) {
            return $this->getProduct()->getFinalPrice();
        }

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
    public function getSelectElement()
    {
        return $this->config->getConfigValue(SettingsInterface::PATH_TO_SELECT_TYPE_ELEMENT_PRODUCT);
    }

    /**
     * @inheritdoc
     */
    public function getNameSelectElement()
    {
        return $this->config->getConfigValue(SettingsInterface::PATH_TO_SELECT_TAG_ELEMENT_PRODUCT);
    }

    /**
     * @inheritdoc
     */
    public function getSerializedTagConfig()
    {
        $tagConfig = [];
        $tagConfig[self::NAME_IS_ENABLE]        = $this->isEnabled();
        $tagConfig[self::NAME_INLINE_CSS]       = $this->getInlineCss();
        $tagConfig[self::NAME_PRICE]            = $this->getPrice();
        $tagConfig[self::NAME_TAG_CODE]         = $this->getTagCode();
        $tagConfig[self::NAME_CURRENCY_CODE]    = $this->getCurrency();
        $tagConfig[self::NAME_NAME_SELECT_TAG]  = $this->getNameSelectElement();
        $tagConfig[self::NAME_SELECT_TAG]       = $this->getSelectElement();

        return $this->jsonHexTagSerializer->serialize($tagConfig);
    }
}
