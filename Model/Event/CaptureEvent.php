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

class CaptureEvent
{
    const CODE = 'capture';

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
   * Handling capture charge and making
   * @param mixed $data
   * @param \Magento\Sales\Model\Order $order
   */
    public function handle($data, $order)
    {
        $statusCode = $this->config->getConfigValue(Config::PATH_TO_STATUS_AFTER_INVOICE);
        if ($statusCode
        && $order->getStatus() != $statusCode
        && $data->orderTotal == $data->totalCaptured) {
            $this->updateInvoice($order, $data);
            $order->addCommentToStatusHistory('Created Invoice and Capture successfully.', $statusCode);
            $this->orderRepository->save($order);
        }
    }

  /**
   * Update order Invoice
   *
   * @param Order $order
   * @param Object
   * @throws Exception
   */
    private function updateInvoice(Order $order, $data)
    {
        /**
         * @var Magento\Sales\Model\Order\Payment $payment
         */
        $payment = $order->getPayment();
        $transaction = $this->serviceTransaction->addTransaction(
            $order,
            TransactionInterface::TYPE_CAPTURE,
            $order->getId().'/capture',
            [
              PaymentTransaction::RAW_DETAILS => [
                  'trans' => $data->transaction->id
              ]
            ]
        );
        $payment->addTransactionCommentsToOrder($transaction, $transaction->getTransactionId());
        $listInvoices = $order->getInvoiceCollection();
        if (count($listInvoices) == 0) {
            if ($order->canInvoice()) {
                $invoice = $this->invoiceService->prepareInvoice($order);
                $invoice->register();
                $invoice->pay();
                $invoice->save();

                $transactionSave = $this->transaction
                ->addObject($invoice)
                ->addObject($invoice->getOrder());
                $transactionSave->save();
                $this->invoiceSender->send($invoice);

                $order->addCommentToStatusHistory(
                    __('Notified customer about invoice creation #%1.', $invoice->getId())
                )->setIsCustomerNotified(false)->save();
            }
        }
        $listInvoices = $order->getInvoiceCollection();
        /**
         * @var InvoiceInterface $lastInvoice
         */
        $lastInvoice = $listInvoices->getLastItem();
        $lastInvoice->setTransactionId($transaction->getTransactionId());
        $this->invoiceRepository->save($lastInvoice);
    }
}
