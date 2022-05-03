<?php
namespace Anyday\Payment\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;

class OrderPlaceAfterObserver implements ObserverInterface
{
  /**
   * @param \Magento\Framework\Event\Observer $observer
   * @return $this
   */
    public function execute(Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        $order->setCanSendNewEmailFlag(false);
    }
}
