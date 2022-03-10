<?php

namespace Anyday\Payment\Model\Event;

class BaseEvent {

  /**
   * @param string $data
   * @param \Magento\Sales\Model\Order $order
   */
  public function getOrder($orderId, $order) {
    if(isset($orderId)) {
      return $order->loadByIncrementId($orderId);
    }
  }

  /** 
   * Mixed data
   */
  public function handled($data, $order) {
  
  }
}