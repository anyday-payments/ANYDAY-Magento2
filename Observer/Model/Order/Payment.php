<?php
declare(strict_types=1);

namespace Anyday\Payment\Observer\Model\Order;

use Anyday\Payment\Service\Anyday\Order;
use Anyday\Payment\Service\Settings\Config;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Store\Model\ScopeInterface;

class Payment implements ObserverInterface
{

    /**
     * @var Order
     */
    private $serviceAnydayOrder;

    /**
     * @var Config
     */
    private $configService;

    /**
     * Payment constructor.
     *
     * @param Order $serviceAnydayOrder
     * @param Config $configService
     */
    public function __construct(
        Order $serviceAnydayOrder,
        Config $configService
    ) {
        $this->serviceAnydayOrder       = $serviceAnydayOrder;
        $this->configService            = $configService;
    }

    /**
     * Update order status
     *
     * @param Observer $observer
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute(Observer $observer)
    {
        $order = $observer->getData('payment')->getOrder();
        if ($order) {
            $this->serviceAnydayOrder->setOrderStatus(
                $order,
                $this->configService->getConfigValue(
                    Config::PATH_TO_NEW_ORDER_STATUS,
                    ScopeInterface::SCOPE_STORE,
                    $order->getStoreId()
                )
            );
        }
    }
}
