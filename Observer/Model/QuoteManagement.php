<?php
declare(strict_types=1);

namespace Anyday\Payment\Observer\Model;

use Anyday\Payment\Gateway\Validator\Availability;
use Anyday\Payment\Service\Anyday\Transaction;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Api\Data\TransactionInterface;
use Magento\Sales\Model\Order;

class QuoteManagement implements ObserverInterface
{
    /**
     * @var Transaction
     */
    private $transactionService;

    /**
     * @param Transaction $transactionService
     */
    public function __construct(
        Transaction $transactionService
    ) {
        $this->transactionService   = $transactionService;
    }

    public function execute(Observer $observer)
    {
        /**
         * @var $order Order
         */
        $order = $observer->getOrder();
        if ($order && $order->getId()) {
            $anydayData = $order->getPayment()->getAdditionalInformation('quote_' . $order->getQuoteId());
            if ($anydayData && isset($anydayData[Availability::NAME_TRANSACTION])) {
                $transaction = $this->transactionService->addTransaction(
                    $order,
                    TransactionInterface::TYPE_AUTH,
                    $order->getId(),
                    [
                        \Magento\Sales\Model\Order\Payment\Transaction::RAW_DETAILS => [
                            'trans' => $anydayData[Availability::NAME_TRANSACTION]
                        ]
                    ]
                );
                $order->getPayment()->addTransactionCommentsToOrder($transaction, $transaction->getTransactionId());
            }
        }
    }
}
