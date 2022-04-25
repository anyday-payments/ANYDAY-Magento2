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
     * OrderAfterSave constructor.
     *
     * @param Registry $registry
     * @param Config $config
     * @param InvoiceRepositoryInterface $invoiceRepository
     * @param OrderRepositoryInterface $orderRepository
     * @param Transaction $serviceTransaction
     */
    public function __construct(
        Registry $registry,
        Config $config,
        InvoiceRepositoryInterface $invoiceRepository,
        OrderRepositoryInterface $orderRepository,
        Transaction $serviceTransaction
    ) {
        $this->registry                 = $registry;
        $this->config                   = $config;
        $this->invoiceRepository        = $invoiceRepository;
        $this->orderRepository          = $orderRepository;
        $this->serviceTransaction       = $serviceTransaction;
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
         * @var $order Order
         */
        $order = $observer->getData('order');
        if ($order->getPayment()->getMethodInstance()->getCode() == ConfigProvider::CODE
            && $this->verifyChangeStatus($order)) {
            if ($this->registry->registry('order_capture_'.$order->getId())) {
                $order = $this->orderRepository->get($order->getId());
                if ($statusCode = $this->config->getConfigValue(Config::PATH_TO_STATUS_AFTER_INVOICE)) {
                    $order->setState($statusCode)->setStatus($statusCode);
                    $this->updateInvoice($order);
                    $this->registry->unregister('order_capture_'.$order->getId());
                    $this->orderRepository->save($order);
                }
            }
        }
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
