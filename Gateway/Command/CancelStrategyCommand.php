<?php
declare(strict_types=1);

namespace Anyday\Payment\Gateway\Command;

use Anyday\Payment\Api\Data\Payment\UrlDataInterface;
use Anyday\Payment\Gateway\Exception\NoData;
use Anyday\Payment\Gateway\Exception\PaymentException;
use Magento\Payment\Gateway\Command;
use Magento\Sales\Api\Data\TransactionInterface;
use Magento\Sales\Model\Order\Payment\Transaction;
use Magento\Sales\Model\Order\Payment\Transaction as PaymentTransaction;
use Magento\Store\Model\ScopeInterface;

class CancelStrategyCommand extends AbstractStrategyCommand
{
    /**
     * Create Refund command
     *
     * @param array $commandSubject
     * @return Command\ResultInterface|void|null
     * @throws NoData|PaymentException
     */
    public function execute(array $commandSubject)
    {
        /**
         * @var $payment Payment
         */
        $payment = $commandSubject['payment']->getPayment();
        $order = $payment->getOrder();
        $this->searchCriteriaBuilder->addFilter('order_id', $order->getId());
        $list = $this->repository->getList(
            $this->searchCriteriaBuilder->create()
        );
        foreach ($list as $oneList) {
            /** @var $oneList Transaction */
            if ($oneList->getTxnType() == TransactionInterface::TYPE_ORDER) {
                $anydayData = $oneList->getAdditionalInformation(Transaction::RAW_DETAILS)['trans'];
                $urlString = UrlDataInterface::URL_ANYDAY .
                    str_replace('{id}', $anydayData, UrlDataInterface::URL_CANCEL);

                $this->curlAnyday->setUrl($urlString);
                $this->curlAnyday->setBody(
                    $this->json->serialize(
                        [
                            'empty' => 1
                        ]
                    )
                );
                $this->curlAnyday->setAuthorization($this->config->getPaymentAutorizeKey(ScopeInterface::SCOPE_STORE, $order->getStoreId()));
                $result = $this->curlAnyday->request();
                if ($result['errorCode'] == 0) {
                    /**
                     * @var Magento\Sales\Model\Order\Payment $payment
                     */
                    $payment = $order->getPayment();
                    $transaction = $this->transaction->addTransaction(
                        $order,
                        TransactionInterface::TYPE_VOID,
                        $order->getId().'-void',
                        [
                            PaymentTransaction::RAW_DETAILS => [
                                'trans' => $result['transactionId']
                            ]
                        ]
                    );
                    $payment->addTransactionCommentsToOrder($transaction, $transaction->getTransactionId());
                    break;
                }
            }
        }
    }
}
