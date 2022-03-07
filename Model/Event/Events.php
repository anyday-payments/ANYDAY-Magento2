<?php

namespace Anyday\Payment\Model;

use Magento\Sales\Model\Order;
use Anyday\Payment\Model\Event\AuthorizeEvent;
use Anyday\Payment\Model\Event\CaptureEvent;
use Anyday\Payment\Model\Event\CancelEvent;
use Anyday\Payment\Model\Event\RefundEvent;

class Events
{
  /**
   * @var array  of event classes that we can handle.
   */
  protected $events = [
      AuthorizeEvent::CODE => AuthorizeEvent::class,
      CaptureEvent::CODE => CaptureEvent::class,
      CancelEvent::CODE => CancelEvent::class,
      RefundEvent::CODE => RefundEvent::class,
  ];

  /**
   * @param \Magento\Sales\Model\Order $order
   */
  public function __construct(
      Order $order
  ) {
      $this->order    = $order;
  }

  /**
   * @param  Object $payload
   *
   * @return mixed
   */
  public function handle($data)
  {
      if (! isset($this->events[$data->transaction->type])) {
          // TODO: Handle in case can't retrieve an event object from '$payload->id'.
          return;
      }

      return (new $this->events[$data->transaction->type])
          ->handle($data, $this->order);
  }
}
