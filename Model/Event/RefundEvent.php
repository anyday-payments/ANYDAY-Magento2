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

class RefundEvent
{
    const CODE = 'refund';

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
   * @var \Magento\Sales\Model\Order\Invoice
   */
    protected $invoice;

  /**
   * @var \Magento\Sales\Model\Order\CreditmemoFactory
   */
    protected $creditMemoFactory;

  /**
   * @var \Magento\Sales\Model\Service\CreditmemoService
   */
    protected $creditMemoService;

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
        $this->_init();
    }

    private function _init()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->invoice = $objectManager->create(\Magento\Sales\Model\Order\Invoice::class);
        $this->creditMemoFactory = $objectManager->create(\Magento\Sales\Model\Order\CreditmemoFactory::class);
        $this->creditMemoService = $objectManager->create(\Magento\Sales\Model\Service\CreditmemoService::class);
    }

  /**
   * Handling refund charge.
   * @param mixed $data
   * @param Order $order
   */
    public function handle($data, $order)
    {
        $statusCode = $this->config->getConfigValue(Config::PATH_TO_STATUS_AFTER_INVOICE);
        if ($statusCode
        && $order->getStatus() == $statusCode
        && $data->totalCaptured > 0
        && $data->totalCaptured == $data->totalRefunded) {
            $invoices = $order->getInvoiceCollection();

            if (count($invoices) == 0) {
                throw new \Magento\Framework\Exception\LocalizedException(__(
                    'No Invoices found for Refund. Magento_ID: %2',
                    $order->getIncrementId()
                ));
            }

            foreach ($invoices as $invoice) {
                $invoice = $this->invoice->loadByIncrementId($invoice->getIncrementId());
                $creditMemo = $this->creditMemoFactory->createByOrder($order);
                $creditMemo->setInvoice($invoice);
                $creditMemo->setCustomerNote(__('Your Order %1 has been refunded.', $order->getIncrementId()));
                $creditMemo->setCustomerNoteNotify(false);
                $creditMemo->addComment(__('Order has been Refunded'));
                $order->setStatus(Order::STATE_CANCELED);
                $this->creditMemoService->refund($creditMemo);
            }
            $this->orderRepository->save($order);
        }
    }
}
