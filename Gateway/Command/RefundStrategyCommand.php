<?php
declare(strict_types=1);

namespace Anyday\Payment\Gateway\Command;

use Anyday\Payment\Api\Data\Payment\UrlDataInterface;
use Anyday\Payment\Gateway\Exception\NoData;
use Anyday\Payment\Gateway\Exception\PaymentException;
use Magento\Payment\Gateway\Command;
use Magento\Sales\Api\Data\TransactionInterface;
use Magento\Sales\Model\Order\Payment\Transaction;
use Magento\Store\Model\ScopeInterface;

class RefundStrategyCommand extends AbstractStrategyCommand
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
        $order =$this->orderRepository->get($commandSubject['payment']->getOrder()->getId());
        $this->searchCriteriaBuilder->addFilter('order_id', $order->getId());
        $list = $this->repository->getList(
            $this->searchCriteriaBuilder->create()
        );
        foreach ($list as $oneList) {
            /** @var $oneList Transaction */
            if ($oneList->getTxnType() == TransactionInterface::TYPE_ORDER) {
                $anydayData = $oneList->getAdditionalInformation(Transaction::RAW_DETAILS)['trans'];
                $urlString = UrlDataInterface::URL_ANYDAY .
                    str_replace('{id}', $anydayData, UrlDataInterface::URL_REFUND);

                $this->curlAnyday->setUrl($urlString);
                $this->curlAnyday->setBody(
                    $this->json->serialize(
                        [
                            'amount' => $commandSubject['amount']
                        ]
                    )
                );
                $this->curlAnyday->setAuthorization(
                    $this->config->getPaymentAutorizeKey(
                        ScopeInterface::SCOPE_STORE,
                        $order->getStoreId()
                    )
                );
                $result = $this->curlAnyday->request();
                if ($result['errorCode'] == 0) {
                    /**
                     * @var Magento\Sales\Model\Order\Payment
                     */
                    $payment = $order->getPayment();
                    $transaction = $this->transaction->addTransaction(
                        $order,
                        TransactionInterface::TYPE_REFUND,
                        $order->getId().'/refund',
                        [
                            Transaction::RAW_DETAILS => [
                            'trans' => $result['transactionId']
                            ]
                        ]
                    );
                    $payment->addTransactionCommentsToOrder($transaction, $transaction->getTransactionId());
                    break;
                } elseif ($result['errorCode'] === 7) {
                    return;
                } else {
                    throw new PaymentException(__($result['errorMessage']));
                }
            }
        }
    }
}
