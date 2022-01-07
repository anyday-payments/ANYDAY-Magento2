<?php
declare(strict_types=1);

namespace Anyday\Payment\Service\Anyday;

use Anyday\Payment\Gateway\Validator\Availability;
use Magento\Sales\Api\Data\TransactionInterface;
use Magento\Sales\Api\TransactionRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Payment\Transaction\BuilderInterface;

class Transaction
{
    /**
     * @var TransactionRepositoryInterface
     */
    private $repositoryTransaction;

    /**
     * @var BuilderInterface
     */
    private $builder;

    /**
     * @param TransactionRepositoryInterface $repositoryTransaction
     * @param BuilderInterface $builder
     */
    public function __construct(
        TransactionRepositoryInterface $repositoryTransaction,
        BuilderInterface $builder
    ) {
        $this->repositoryTransaction    = $repositoryTransaction;
        $this->builder                  = $builder;
    }

    /**
     * @param Order $order
     * @param string $typeTxn
     * @param array $additionalInformation
     * @param string $tnxId
     * @return TransactionInterface|bool
     */
    public function addTransaction(
        Order $order,
        string $typeTxn = TransactionInterface::TYPE_AUTH,
        string $tnxId = '',
        array $additionalInformation = []
    ) {
        $transaction = false;
        if ($order && $order->getId()) {
            $anydayData = $order->getPayment()->getAdditionalInformation('quote_' . $order->getQuoteId());
            if ($anydayData && isset($anydayData[Availability::NAME_TRANSACTION])) {
                $payment = $order->getPayment();
                if ($tnxId == '') {
                    $tnxId = $order->getId();
                }
                $transaction = $this->builder->setPayment($payment)
                    ->setOrder($order)
                    ->setTransactionId($tnxId)
                    ->setFailSafe(true);
                if (count($additionalInformation)) {
                    $transaction->setAdditionalInformation(
                        [Order\Payment\Transaction::RAW_DETAILS => [
                            'trans' => $anydayData[Availability::NAME_TRANSACTION]
                        ]]
                    );
                }
                $transaction = $transaction->build($typeTxn);
                if ($typeTxn == TransactionInterface::TYPE_AUTH) {
                    $transaction->setIsClosed(false);
                }
                $this->repositoryTransaction->save($transaction);
            }
        }

        return $transaction;
    }
}
