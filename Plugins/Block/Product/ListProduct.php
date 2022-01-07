<?php
declare(strict_types=1);

namespace Anyday\Payment\Plugins\Block\Product;

use Anyday\Payment\Service\Anyday\Category\Settings;
use Magento\Catalog\Model\Product;
use Magento\CatalogWidget\Block\Product\ProductsList;
use Magento\Framework\Exception\NoSuchEntityException;

class ListProduct
{
    /**
     * @var Settings
     */
    private $settings;

    /**
     * @param Settings $settings
     */
    public function __construct(
        Settings $settings
    ) {
        $this->settings = $settings;
    }

    /**
     * Insert Price tag Code
     *
     * @param \Magento\Catalog\Block\Product\ListProduct $subject
     * @param string $result
     * @param Product $product
     * @return string
     * @throws NoSuchEntityException
     */
    public function afterGetProductPrice(
        \Magento\Catalog\Block\Product\ListProduct $subject,
        string $result,
        Product $product
    ) {
        if ($subject) {
            return  $result . $this->settings->getPriceTagHtml($product->getFinalPrice());
        }

        return $result;
    }

    /**
     * Insert Price tag
     *
     * @param ProductsList $subject
     * @param string $result
     * @return string
     */
    public function afterGetProductPriceHtml(
        ProductsList $subject,
        $result
    ) {
        if ($subject) {
            return $result;
        }

        return $result;
    }
}
