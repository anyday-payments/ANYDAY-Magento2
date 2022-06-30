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
use Magento\Store\Model\ScopeInterface;

class AuthorizeEvent
{
    const CODE = 'authorize';

  /**
   * @var Config
   */
    private $config;

  /**
   * @var Transaction
   */
    private $serviceTransaction;

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
   * Handling authorize charge.
   * @param mixed $data
   * @param Order $order
   */
    public function handle($data, $order)
    {
        $statusCode = $this->config->getConfigValue(
            Config::PATH_TO_NEW_ORDER_STATUS,
            ScopeInterface::SCOPE_STORE,
            $order->getStoreId()
        );
        if ($statusCode && $order->getStatus() == $statusCode) {
            /**
             * @var Magento\Sales\Model\Order\Payment
             */
            $payment = $order->getPayment();
            $transaction = $this->serviceTransaction->addTransaction(
                $order,
                TransactionInterface::TYPE_ORDER,
                $order->getId() . '/order',
                [
                PaymentTransaction::RAW_DETAILS => [
                  'trans' => $data->id
                ]
                ]
            );
            $payment->addTransactionCommentsToOrder($transaction, $transaction->getTransactionId());
            $transaction = $this->serviceTransaction->addTransaction(
                $order,
                TransactionInterface::TYPE_AUTH,
                $order->getId(),
                [
                PaymentTransaction::RAW_DETAILS => [
                  'trans' => $data->transaction->id
                ]
                ]
            );
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $orderSender = $objectManager->create(\Magento\Sales\Model\Order\Email\Sender\OrderSender::class);
            $orderSender->send($order, true);
            $afterPaymentStatus = $this->config->getConfigValue(
                Config::PATH_TO_STATUS_AFTER_PAYMENT,
                ScopeInterface::SCOPE_STORE,
                $order->getStoreId()
            );
            $payment->addTransactionCommentsToOrder($transaction, $transaction->getTransactionId());
            $order->setStatus($afterPaymentStatus);
            $this->orderRepository->save($order);
        }
    }
}
