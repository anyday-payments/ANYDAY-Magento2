<?php
declare(strict_types=1);

namespace Anyday\Payment\Service\Settings;

use Anyday\Payment\Api\Data\Anydaytag\SettingsInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class Config implements SettingsInterface
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var ScopeInterface
     */
    private $scopeInterface;

    /**
     * @var UrlInterface
     */
    private $urlInterface;

    /**
     * Config constructor.
     *
     * @param StoreManagerInterface $storeManager
     * @param ScopeConfigInterface $scopeInterface
     * @param UrlInterface $urlInterface
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeInterface,
        UrlInterface $urlInterface
    ) {
        $this->storeManager     = $storeManager;
        $this->scopeInterface   = $scopeInterface;
        $this->urlInterface     = $urlInterface;
    }

    /**
     * Get tag token
     *
     * @param null|string $store
     * @return string
     */
    public function getTagToken($store = null)
    {
        return $this->scopeInterface->getValue(
            self::PATH_TO_TAG_TOKEN,
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
            $store
        );
    }

    /**
     * Get Currency Code
     *
     * @param int|null $store
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function getCurrencyCode($store = null)
    {
        return $this->storeManager->getStore()->getCurrentCurrencyCode($store);
    }

    /**
     * Get is enable Tag module
     *
     * @param null|string $store
     * @return bool|void
     */
    public function isTagModuleEnable($store = null)
    {
        if ($this->scopeInterface->getValue(
            self::PATH_ENABLE_TAG_MODULE,
            ScopeInterface::SCOPE_STORE,
            $store
        ) == '1') {
            return true;
        }

        return false;
    }

    /**
     * Return Config Value
     *
     * @param string $path
     * @param string $scope
     * @param null|string $storeId
     * @return mixed
     */
    public function getConfigValue(string $path, $scope = ScopeInterface::SCOPE_STORE, $storeId = null)
    {
        return $this->scopeInterface->getValue(
            $path,
            $scope,
            $storeId
        );
    }

    /**
     * Get Success url
     *
     * @param string $quoteId
     * @return string
     */
    public function getSuccesRedirect($quoteId)
    {
        return $this->urlInterface->getUrl(
            'anydayfront/payment/succes',
            ['quote'=>$quoteId]
        );
    }

    /**
     * Get Cancel Url
     *
     * @param string $quoteId
     * @return string
     */
    public function getCancelRedirect($quoteId)
    {
        return $this->urlInterface->getUrl(
            'anydayfront/payment/cancel',
            ['quote'=>$quoteId]
        );
    }

    /**
     * Get Autorize Key
     *
     * @param string $scope
     * @param null|string $storeId
     * @return mixed
     */
    public function getPaymentAutorizeKey($scope = ScopeInterface::SCOPE_STORE, $storeId = null)
    {
        if ($this->getConfigValue(self::PATH_TO_PAYMENT_MODE_TYPE, $scope, $storeId) == '1') {
            return $this->getConfigValue(self::PATH_TO_TOKEN_LIVE, $scope, $storeId);
        }

        return $this->getConfigValue(self::PATH_TO_TOKEN_SANDBOX, $scope, $storeId);
    }

    public function getPricetagLanguage($scope = ScopeInterface::SCOPE_STORE, $storeId = null) {
        return $this->scopeInterface->getValue(
            self::PATH_TO_JS_LOCALE,
            $scope,
            $storeId
        );
    }
}
