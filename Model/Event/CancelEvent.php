<?php
namespace Anyday\Payment\Model\Event;

use Anyday\Payment\Service\Settings\Config;
use Magento\Sales\Model\Order;
use Magento\Sales\Api\Data\TransactionInterface;
use Magento\Sales\Model\Order\Payment\Transaction as PaymentTransaction;
use Anyday\Payment\Service\Anyday\Transaction;
use Magento\Sales\Api\InvoiceRepositoryInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Service\InvoiceService;
use Magento\Framework\DB\Transaction as MagentoTransaction;
use Magento\Sales\Model\Order\Email\Sender\InvoiceSender;
use Magento\Sales\Api\Data\InvoiceInterface;

class CancelEvent
{
    const CODE = 'cancel';

  /**
   * @var Config
   */
    private $config;

  /**
   * @var Transaction
   */
    private $serviceTransaction;

  /**
   * @var InvoiceRepositoryInterface
   */
    private $invoiceRepository;

  /**
   * @var OrderRepositoryInterface
   */
    private $orderRepository;

  /**
   * @var InvoiceService
   */
    protected $invoiceService;
  /**
   * @var MagentoTransaction
   */
    protected $transaction;
  /**
   * @var InvoiceSender
   */
    protected $invoiceSender;

  /**
   * @param Config $config
   * @param Transaction $serviceTransaction
   * @param InvoiceRepositoryInterface $invoiceRepository
   * @param OrderRepositoryInterface $orderRepository
   * @param InvoiceService $invoiceService
   * @param InvoiceSender $invoiceSender
   * @param MagentoTransaction $transaction
   */
    public function __construct(
        Config $config,
        Transaction $serviceTransaction,
        InvoiceRepositoryInterface $invoiceRepository,
        OrderRepositoryInterface $orderRepository,
        InvoiceService $invoiceService,
        InvoiceSender $invoiceSender,
        MagentoTransaction $transaction
    ) {
        $this->config             = $config;
        $this->serviceTransaction = $serviceTransaction;
        $this->invoiceRepository  = $invoiceRepository;
        $this->orderRepository    = $orderRepository;
        $this->invoiceService     = $invoiceService;
        $this->transaction        = $transaction;
        $this->invoiceSender      = $invoiceSender;
    }

  /**
   * Handling cancel charge.
   * @param mixed $data
   * @param Order $order
   */
    public function handle($data, $order)
    {
        if ($order->getStatus() != Order::STATE_CANCELED) {
            $this->saveTransaction($data, $order);
            $this->orderRepository->save($order);
            $order->setState(Order::STATE_CANCELED)->setStatus(Order::STATE_CANCELED);
            $this->orderRepository->save($order);
        }
    }

    /**
     * Saving the transaction
     * @param mixed $data
     * @param Order $order
     */
    private function saveTransaction($data, $order)
    {
      /**
       * @var Magento\Sales\Model\Order\Payment $payment
       */
        $payment = $order->getPayment();
        $transaction = $this->serviceTransaction->addTransaction(
            $order,
            TransactionInterface::TYPE_VOID,
            $order->getId().'-void',
            [
            PaymentTransaction::RAW_DETAILS => [
                'trans' => $data->transaction->id
            ]
            ]
        );
        $payment->addTransactionCommentsToOrder($transaction, $transaction->getTransactionId());
    }
}
