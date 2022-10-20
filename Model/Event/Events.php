<?php

namespace Anyday\Payment\Model\Event;

use Magento\Sales\Model\Order;
use Anyday\Payment\Model\Event\AuthorizeEvent;
use Anyday\Payment\Model\Event\CaptureEvent;
use Anyday\Payment\Model\Event\CancelEvent;
use Anyday\Payment\Model\Event\RefundEvent;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\FilterBuilder;
use Magento\Sales\Model\Order\Payment\Transaction\Repository as TransactionRepository;
use Magento\Sales\Model\Order\Payment\Transaction;
use Anyday\Payment\Service\Anyday\Transaction as AnydayTransaction;
use Anyday\Payment\Service\Settings\Config;
use Magento\Sales\Api\InvoiceRepositoryInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Service\InvoiceService;
use Magento\Framework\DB\Transaction as MagentoTransaction;
use Magento\Sales\Model\Order\Email\Sender\InvoiceSender;
use Magento\Sales\Api\OrderManagementInterface;

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
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var FilterBuilder
     */
    protected $filterBuilder;

    /**
     * @var TransactionRepository
     */
    protected $transactionRepository;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var AnydayTransaction
     */
    protected $serviceTransaction;

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
     * @var Transaction
     */
    protected $paymentTransaction;

    /**
     * @var OrderManagementInterface
     */
    protected $orderManagement;

    /**
     * @param Order $order
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param FilterBuilder $filterBuilder
     * @param TransactionRepository $transactionRepository
     * @param Config $config
     * @param AnydayTransaction $serviceTransaction
     * @param InvoiceRepositoryInterface $invoiceRepository
     * @param OrderRepositoryInterface $orderRepository
     * @param InvoiceService $invoiceService
     * @param InvoiceSender $invoiceSender
     * @param MagentoTransaction $transaction
     * @param Transaction $paymentTransaction
     */
    public function __construct(
        Order $order,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        FilterBuilder $filterBuilder,
        TransactionRepository $transactionRepository,
        Config $config,
        AnydayTransaction $serviceTransaction,
        InvoiceRepositoryInterface $invoiceRepository,
        OrderRepositoryInterface $orderRepository,
        InvoiceService $invoiceService,
        InvoiceSender $invoiceSender,
        MagentoTransaction $transaction,
        Transaction $paymentTransaction,
        OrderManagementInterface $orderManagement
    ) {
        $this->order    = $order;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
        $this->transactionRepository = $transactionRepository;
        $this->config = $config;
        $this->serviceTransaction = $serviceTransaction;
        $this->invoiceRepository  = $invoiceRepository;
        $this->orderRepository    = $orderRepository;
        $this->invoiceService = $invoiceService;
        $this->transaction = $transaction;
        $this->invoiceSender = $invoiceSender;
        $this->paymentTransaction = $paymentTransaction;
        $this->orderManagement = $orderManagement;
    }

    /**
     * @param  Object $payload
     * @return mixed
     */
    public function handle($data)
    {
        if (is_null($data->transaction) || (! isset($this->events[$data->transaction->type])
            && $data->transaction->status != "success")) {
            if($data->cancelled === true) {
                $cancel = (new $this->events["cancel"](
                    $this->config,
                    $this->serviceTransaction,
                    $this->invoiceRepository,
                    $this->orderRepository,
                    $this->invoiceService,
                    $this->invoiceSender,
                    $this->transaction,
                    $this->paymentTransaction
                )
                )->handle($data, $this->order);
                if($cancel)
                    $this->orderManagement->cancel($this->getOrder($data->orderId)->getId());
            }
            return;
        }
        $dataTxnId = $data->transaction;
        $order = $this->getOrder($data->orderId);
        if ($order && !$this->handled($dataTxnId->id, $order)) {
            return (new $this->events[$data->transaction->type](
                $this->config,
                $this->serviceTransaction,
                $this->invoiceRepository,
                $this->orderRepository,
                $this->invoiceService,
                $this->invoiceSender,
                $this->transaction,
                $this->paymentTransaction
            )
            )->handle($data, $this->order);
        }
    }

    /**
     * Returns order object from increment id.
     * @param string $orderId
     */
    public function getOrder($orderId)
    {
        if (isset($orderId)) {
            return $this->order->loadByIncrementId($orderId);
        }
    }

    /**
     * Checks if transaction is handled and returns true if handled.
     * @param DataObject $data
     * @param \Magento\Sales\Model\Order $order
     * @return boolean
     */
    public function handled($dataTxnId, $order)
    {
        $transactionList = $this->getAllTransactionList($order);
        $handled         = false;
        foreach ($transactionList as $id => $transaction) {
            if ($dataTxnId == $transaction->getAdditionalInformation(Transaction::RAW_DETAILS)['trans']) {
                $handled = true;
            }
        }
        return $handled;
    }

    /**
     * Returns all transactions attached with order
     * @param \Magento\Sales\Model\Order $order
     * @return \Magento\Sales\Api\Data\TransactionSearchResultInterface
     */
    public function getAllTransactionList($order)
    {
        $filters[] = $this->filterBuilder->setField('payment_id')
        ->setValue($order->getPayment()->getId())
        ->create();
        $filters[] = $this->filterBuilder->setField('order_id')
        ->setValue($order->getId())
        ->create();
        $searchCriteria = $this->searchCriteriaBuilder->addFilters($filters)->create();
        return $this->transactionRepository->getList($searchCriteria);
    }
}
