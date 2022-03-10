<?php 
namespace Anyday\Payment\Model\Event;

class CaptureEvent extends BaseEvent{
  const CODE = 'capture';
/**
 * Handling capture charge and making 
 * @param mixed $data
 * @param \Magento\Sales\Model\Order $order
 */
  public function handle($data, $order) {
    $order = $this->getOrder($data->orderId, $order);
    if($order && $this->handled($data)) {
      
    }
  }
}