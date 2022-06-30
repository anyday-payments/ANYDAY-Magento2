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
                    $this->registry->unregister('order_capture_'.$order->getId());
                    $order->setStatus($statusCode);
                    $this->orderRepository->save($order);
                }
            }
        }
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
