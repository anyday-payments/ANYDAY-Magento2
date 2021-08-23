<?php
declare(strict_types=1);

namespace Anyday\PaymentAndTrack\Observer\Adminhtml;

use Anyday\PaymentAndTrack\Api\Data\Andytag\SettingsInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class Paymentchange implements ObserverInterface
{
    const NAME_FIND_VALUE = 'changed_paths';

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var WriterInterface
     */
    private $writer;

    /**
     * Paymentchange constructor.
     *
     * @param RequestInterface $request
     * @param WriterInterface $writer
     */
    public function __construct(
        RequestInterface $request,
        WriterInterface $writer
    ) {
        $this->request  = $request;
        $this->writer   = $writer;
    }

    /**
     * Update path to save
     *
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $findTag = false;
        $listChanged = $observer->getData(self::NAME_FIND_VALUE);
        if(is_array($listChanged)) {
            $findTag = array_search(SettingsInterface::PATH_TO_TAG_TOKEN, $listChanged);
        }
        if ($findTag !== false) {
            unset($listChanged[$findTag]);
            $observer->setData(self::NAME_FIND_VALUE, $listChanged);
        }
    }
}
