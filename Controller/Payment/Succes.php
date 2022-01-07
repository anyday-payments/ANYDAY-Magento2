<?php
declare(strict_types=1);

namespace Anyday\Payment\Controller\Payment;

use Anyday\Payment\Gateway\Validator\Availability;
use Anyday\Payment\Service\Settings\Config;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Sales\Api\Data\TransactionInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Payment\Transaction;

class Succes extends Action
{
    /**
     * @var Session
     */
    private $checkoutSession;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var \Anyday\Payment\Service\Anyday\Order
     */
    private $serviceAnydayOrder;

    /**
     * @var Config
     */
    private $configService;

    /**
     * @var \Anyday\Payment\Service\Anyday\Transaction
     */
    private $serviceTransaction;

    /**
     * Succes constructor.
     *
     * @param Context $context
     * @param Session $checkoutSession
     * @param OrderRepositoryInterface $orderRepository
     * @param \Anyday\Payment\Service\Anyday\Order $serviceAnydayOrder
     * @param Config $configService
     * @param \Anyday\Payment\Service\Anyday\Transaction $serviceTransaction
     */
    public function __construct(
        Context $context,
        Session $checkoutSession,
        OrderRepositoryInterface $orderRepository,
        \Anyday\Payment\Service\Anyday\Order $serviceAnydayOrder,
        Config $configService,
        \Anyday\Payment\Service\Anyday\Transaction $serviceTransaction
    ) {
        parent::__construct($context);
        $this->checkoutSession          = $checkoutSession;
        $this->orderRepository          = $orderRepository;
        $this->serviceAnydayOrder       = $serviceAnydayOrder;
        $this->configService            = $configService;
        $this->serviceTransaction       = $serviceTransaction;
    }

    /**
     * Update status order and add transaction
     *
     * @return ResponseInterface|Redirect|ResultInterface
     */
    public function execute()
    {
        $orderId = $this->checkoutSession->getLastOrderId();
        /** @var Order $order */
        $order = $orderId ? $this->orderRepository->get($orderId) : false;
        if ($order && $order->getId()) {
            $anydayData = $order->getPayment()->getAdditionalInformation('quote_' . $order->getQuoteId());
            if ($anydayData && isset($anydayData[Availability::NAME_TRANSACTION])) {
                $payment = $order->getPayment();
                $transaction = $this->serviceTransaction->addTransaction(
                    $order,
                    TransactionInterface::TYPE_ORDER,
                    $order->getId() . '/order',
                    [
                        Transaction::RAW_DETAILS => [
                            'trans' => $anydayData[Availability::NAME_TRANSACTION]
                        ]
                    ]
                );
                $payment->addTransactionCommentsToOrder($transaction, $transaction->getTransactionId());
                $this->serviceAnydayOrder->setOrderStatus(
                    $order,
                    $this->configService->getConfigValue(Config::PATH_TO_STATUS_AFTER_PAYMENT)
                );
            } else {
                $this->messageManager->addErrorMessage(
                    __('Not Find Transaction Anyday.')
                );
            }
        }
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('checkout/onepage/success');
    }
}
