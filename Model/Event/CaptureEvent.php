<?php 
namespace Anyday\Payment\Model\Event;

class CaptureEvent {
  const CODE = 'capture';
/**
 * Handling capture charge and making 
 */
  public function handle($data, $order) {
    $order = $order->loadByIncrementId($data->orderId);
    if (! $order->getId()) {
        // TODO: Handle in case of improper response structure.
        return;
    }

    if (! $payment = $order->getPayment()) {
        // TODO: Handle in case of improper response structure.
        return;
    }

    $paymentMethod = $payment->getMethod();
    if($this->handled($data) && ) {

    }
  }
}