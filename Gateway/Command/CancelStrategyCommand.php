<?php
declare(strict_types=1);

namespace Anyday\PaymentAndTrack\Gateway\Command;

use Anyday\PaymentAndTrack\Api\Data\Payment\UrlDataInterface;
use Anyday\PaymentAndTrack\Gateway\Exception\NoData;
use Anyday\PaymentAndTrack\Gateway\Exception\PaymentException;
use Magento\Payment\Gateway\Command;
use Magento\Sales\Api\Data\TransactionInterface;
use Magento\Sales\Model\Order\Payment;
use Magento\Sales\Model\Order\Payment\Transaction;

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
            if ($oneList->getTxnType() == TransactionInterface::TYPE_AUTH) {
                $anydayData = $oneList->getAdditionalInformation(Transaction::RAW_DETAILS)['trans'];
                $urlString = UrlDataInterface::URL_ANYDAY_PAYMENT .
                    str_replace('{id}', $anydayData, UrlDataInterface::URL_CANCEL);

                $this->curlAnyday->setUrl($urlString);
                $this->curlAnyday->setBody(
                    $this->json->serialize(
                        [
                            'empty' => 1
                        ]
                    )
                );
                $this->curlAnyday->setAuthorization($this->config->getPaymentAutorizeKey());
                $result = $this->curlAnyday->request();
                if ($result['errorCode'] == 0) {
                    break;
                } else {
                    throw new PaymentException(__($result['errorMessage']));
                }
            }
        }
    }
}
