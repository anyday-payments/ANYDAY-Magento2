<?php
declare(strict_types=1);

namespace Anyday\PaymentAndTrack\Block\Adminhtml\Config;

use Magento\Backend\Block\Template;
use Magento\Directory\Helper\Data as DirectoryHelper;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Store\Model\StoreManagerInterface;

class Store extends Template
{
    const NAME_STORE    = 'scope';
    const NAME_WEBSITE  = 'website';
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var Json
     */
    private $json;

    /**
     * Store constructor.
     *
     * @param Template\Context $context
     * @param StoreManagerInterface $storeManager
     * @param Json $json
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        StoreManagerInterface $storeManager,
        Json $json,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->storeManager = $storeManager;
        $this->json         = $json;
    }

    /**
     * RETURN JSON CONFIG
     *
     * @return bool|string
     */
    public function getStoreJson()
    {
        $retArr = [
            'type'  => self::NAME_STORE
        ];
        if ($webSiteId = $this->_request->getParam('website', 0)) {
            $retArr['type'] = self::NAME_WEBSITE;
            $retArr['id']   = $webSiteId;
        } else {
            $retArr['id'] = $this->_request->getParam('store', 0);
        }

        return $this->json->serialize($retArr);
    }
}
