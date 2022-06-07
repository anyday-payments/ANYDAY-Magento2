<?php
declare(strict_types=1);

namespace Anyday\Payment\Observer\Adminhtml;

use Anyday\Payment\Model\Ui\ConfigProvider;
use Anyday\Payment\Service\Anyday\Transaction;
use Anyday\Payment\Service\Settings\Config;
use Exception;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Registry;
use Magento\Sales\Api\Data\TransactionInterface;
use Magento\Sales\Model\Order\Payment\Transaction as PaymentTransaction;
use Magento\Sales\Api\InvoiceRepositoryInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Config as OrderConfig;
use Magento\Sales\Model\Order\Status\HistoryFactory;
use Magento\Store\Model\ScopeInterface;

class OrderAfterSave implements ObserverInterface
{
    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var InvoiceRepositoryInterface
     */
    private $invoiceRepository;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var Transaction
     */
    private $serviceTransaction;

    /**
     * @var OrderConfig
     */
    protected $orderConfig;

    /**
     * @var HistoryFactory
     */
    protected $orderHistoryFactory;

    /**
     * OrderAfterSave constructor.
     *
     * @param Registry $registry
     * @param Config $config
     * @param InvoiceRepositoryInterface $invoiceRepository
     * @param OrderRepositoryInterface $orderRepository
     * @param Transaction $serviceTransaction
     * @param OrderConfig $orderConfig
     * @param HistoryFactory $orderHistoryFactory
     */
    public function __construct(
        Registry $registry,
        Config $config,
        InvoiceRepositoryInterface $invoiceRepository,
        OrderRepositoryInterface $orderRepository,
        Transaction $serviceTransaction,
        OrderConfig $orderConfig,
        HistoryFactory $orderHistoryFactory
    ) {
        $this->registry                 = $registry;
        $this->config                   = $config;
        $this->invoiceRepository        = $invoiceRepository;
        $this->orderRepository          = $orderRepository;
        $this->serviceTransaction       = $serviceTransaction;
        $this->orderConfig              = $orderConfig;
        $this->orderHistoryFactory      = $orderHistoryFactory;
    }

    /**
     * Add order comment and update capture
     *
     * @param Observer $observer
     * @throws Exception
     */
    public function execute(Observer $observer)
    {
        /**
         * @var Order
         */
        $order = $observer->getData('order');
        if ($order->getPayment()->getMethodInstance()->getCode() == ConfigProvider::CODE
            && $this->verifyChangeStatus($order)) {
            if ($this->registry->registry('order_capture_'.$order->getId())) {
                if ($statusCode = $this->config->getConfigValue(
                    Config::PATH_TO_STATUS_AFTER_INVOICE,
                    ScopeInterface::SCOPE_STORE,
                    $order->getStoreId()
                )
                ) {
                    //@TODO remove this function
                    //$this->updateInvoice($order);
                    $this->registry->unregister('order_capture_'.$order->getId());
                    $order = $this->addCommentToStatusHistory('Creating Invoice and Capture.', $order, $statusCode);
                    $this->orderRepository->save($order);
                }
            }
        }
    }

    /**
     * Add a comment to order status history.
     *
     * Different or default status may be specified.
     *
     * @param string $comment
     * @param Order $order
     * @param bool|string $status
     * @return Order
     */
    public function addCommentToStatusHistory($comment, $order, $status = false)
    {
        if (false === $status) {
            $status = $order->getStatus();
        } elseif (true === $status) {
            $status = $this->orderConfig->getStateDefaultStatus($order->getState());
        } else {
            $order->setStatus($status);
        }
        $history = $this->orderHistoryFactory->create()->setStatus(
            $status
        )->setComment(
            $comment
        )->setEntityName(
            'order'
        )->setIsVisibleOnFront(
            false
        );
        return $this->addStatusHistory($history, $order);
    }

    /**
     * Adds the object to the status history collection, which is automatically saved when the order is saved.
     * See the entity_id attribute backend model.
     * Or the history record can be saved standalone after this.
     *
     * @param \Magento\Sales\Model\Order\Status\History $history
     * @param Order $order
     * @return Order
     */
    public function addStatusHistory(\Magento\Sales\Model\Order\Status\History $history, $order)
    {
        $history->setOrder($order);
        $order->setStatus($history->getStatus());
        if (!$history->getId()) {
            $order->setStatusHistories(array_merge($order->getStatusHistories(), [$history]));
            $order->setDataChanges(true);
        }
        return $order;
    }

    /**
     * Update order Invoice
     *
     * @param Order $order
     * @throws Exception
     */
    private function updateInvoice(Order $order)
    {
        $payment = $order->getPayment();
        $transaction = $this->serviceTransaction->addTransaction(
            $order,
            TransactionInterface::TYPE_CAPTURE,
            $order->getId().'-capture',
            [
                PaymentTransaction::RAW_DETAILS => [
                    'trans' => $this->registry->registry('order_capture_'.$order->getId())
                ]
            ]
        );
        $payment->addTransactionCommentsToOrder($transaction, $transaction->getTransactionId());
        $listInvoices = $order->getInvoiceCollection();
        /**
         * @var $lastInvoice Invoice
         */
        $lastInvoice = $listInvoices->getLastItem();
        $lastInvoice->setTransactionId($transaction->getTransactionId());
        $this->invoiceRepository->save($lastInvoice);
    }

    /**
     * Verifing change status
     *
     * @param Order $order
     * @return bool
     */
    private function verifyChangeStatus(Order $order): bool
    {
        if ($order->canInvoice()) {
            return false;
        }

        return true;
    }
}
