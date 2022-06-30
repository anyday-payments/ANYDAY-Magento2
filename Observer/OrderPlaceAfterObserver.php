<?php
namespace Anyday\Payment\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Anyday\Payment\Model\Ui\ConfigProvider;

class OrderPlaceAfterObserver implements ObserverInterface
{
  /**
   * @param \Magento\Framework\Event\Observer $observer
   * @return $this
   */
    public function execute(Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        $payment = $order->getPayment();
        if ($payment->getMethod() === ConfigProvider::CODE) {
            $order->setCanSendNewEmailFlag(false);
        }
    }
}
