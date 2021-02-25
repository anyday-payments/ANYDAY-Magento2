<?php
declare(strict_types=1);

namespace Anyday\PaymentAndTrack\Gateway\Response;

use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Payment\Gateway\Response\HandlerInterface;
use Magento\Sales\Model\Order\Payment;

class FraudHandler implements HandlerInterface
{
    const ANYDAY_MSG_LIST   = 'ANYDAY_MSG_LIST';
    const FRAUD_MSG_LIST    = 'fraud';

    /**
     * Handles fraud messages
     *
     * @param array $handlingSubject
     * @param array $response
     * @return void
     */
    public function handle(array $handlingSubject, array $response)
    {
        if (!isset($response[self::ANYDAY_MSG_LIST]) || !is_array($response[self::ANYDAY_MSG_LIST])) {
            return;
        }

        if (!isset($handlingSubject['payment'])
            || !$handlingSubject['payment'] instanceof PaymentDataObjectInterface
        ) {
            throw new \InvalidArgumentException('Payment data object should be provided');
        }

        /** @var PaymentDataObjectInterface $paymentDO */
        $paymentDO = $handlingSubject['payment'];
        $payment = $paymentDO->getPayment();

        $payment->setAdditionalInformation(
            self::ANYDAY_MSG_LIST,
            (array)$response[self::ANYDAY_MSG_LIST]
        );

        /** @var $payment Payment */
        $payment->setIsTransactionPending(true);
        $payment->setIsAnydayDetected(true);
    }
}
