<?php
declare(strict_types=1);

namespace Anyday\Payment\Block\Catalog\Product;

use Anyday\Payment\Api\Data\Anydaytag\PricetagInterface;
use Anyday\Payment\Api\Data\Anydaytag\SettingsInterface;
use Anyday\Payment\Service\Settings\Config;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Block\Product\View;
use Magento\Catalog\Helper\Product;
use Magento\Catalog\Model\ProductTypes\ConfigInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\Locale\FormatInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Anyday\Payment\Lib\Serialize\Serializer\JsonHexTag;
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
     * @param JsonHexTag $jsonHexTagSerializer
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
        JsonHexTag $jsonHexTagSerializer,
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
     * @return string
     */
    public function getPricetagLanguage() {
        return "https://my.anyday.io/webshopPriceTag/anyday-price-tag-".$this->config->getPricetagLanguage()."-es2015.js";
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
        $product = $this->getProduct();
        if (null !== $product) {
            return $product->getPriceInfo()->getPrice('final_price')->getAmount()->getValue();
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

        return $this->jsonHexTagSerializer->serialize($tagConfig);
    }
}
