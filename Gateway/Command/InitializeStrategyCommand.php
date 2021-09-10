<?php
namespace Anyday\PaymentAndTrack\Gateway\Command;

use Anyday\PaymentAndTrack\Api\Data\Payment\UrlDataInterface;
use Anyday\PaymentAndTrack\Gateway\Exception\NoData;
use Anyday\PaymentAndTrack\Gateway\Http\Client\Curl;
use Anyday\PaymentAndTrack\Service\Settings\Config;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Payment\Gateway\CommandInterface;
use Magento\Payment\Gateway\Validator\ResultInterface;
use Magento\Payment\Gateway\Validator\ResultInterfaceFactory;
use Magento\Sales\Model\Order\Payment;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Directory\Model\Currency;
use \Anyday\PaymentAndTrack\Service\Anyday\Order;

class InitializeStrategyCommand implements CommandInterface
{
    const NAME_URL          = 'url';
    const NAME_TRANSACTION  = 'transactionId';
    const NAME_QUOTE        = 'quoteId';
    const NAME_AMOUNT       = 'amount';

    /**
     * @var ResultInterfaceFactory
     */
    private $resultInterfaceFactory;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var Json
     */
    private $json;

    /**
     * @var Curl
     */
    private $curlAnyday;

    /**
     * @var \Anyday\PaymentAndTrack\Service\Anyday\Order
     */
    private $serviceAnydayOrder;

    /**
     * @var Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var Magento\Directory\Model\Currency
     */
    protected $currencySymbol;

    /**
     * InitializeStrategyCommand constructor.
     *
     * @param ResultInterfaceFactory $resultInterfaceFactory
     * @param Config $config
     * @param Json $json
     * @param Curl $curlAnyday
     * @param Order $serviceAnydayOrder
     * @param StoreManagerInterface $storeManager
     * @param Currency $currencySymbol
     */
    public function __construct(
        ResultInterfaceFactory $resultInterfaceFactory,
        Config $config,
        Json $json,
        Curl $curlAnyday,
        Order $serviceAnydayOrder,
        StoreManagerInterface $storeManager,
        Currency $currencySymbol
    ) {
        $this->config                   = $config;
        $this->json                     = $json;
        $this->curlAnyday               = $curlAnyday;
        $this->resultInterfaceFactory   = $resultInterfaceFactory;
        $this->serviceAnydayOrder       = $serviceAnydayOrder;
        $this->_storeManager            = $storeManager;
        $this->currencySymbol           = $currencySymbol;
    }

    /**
     * Initialize order
     *
     * @param array $commandSubject
     * @return ResultInterface
     * @throws AlreadyExistsException
     * @throws InputException
     * @throws NoSuchEntityException
     */
    public function execute(array $commandSubject)
    {
        /**
         * @var $order Order
         */
        $order = $commandSubject['payment']->getPayment()->getOrder();
        if ($order) {
            /** @var Payment $payment */
            $payment = $commandSubject['payment']->getPayment();
            if ($this->isNotVerifed($payment)) {
                try {
                    $sendParam = [
                        'Amount' => $payment->getOrder()->getGrandTotal(),
                        'Currency' => $this->_storeManager->getStore()->getCurrentCurrencyCode(),
                        'OrderId' => (string)$payment->getOrder()->getIncrementId(),
                        'SuccessRedirectUrl' => $this->config->getSuccesRedirect($payment->getOrder()->getQuoteId()),
                        'CancelPaymentRedirectUrl' => $this->config->getCancelRedirect(
                            $payment->getOrder()->getQuoteId()
                        )
                    ];
                    $this->curlAnyday->setBody($this->json->serialize($sendParam));
                    $this->curlAnyday->setUrl(UrlDataInterface::URL_AUTORIZE);
                    $this->curlAnyday->setAuthorization($this->config->getPaymentAutorizeKey());
                    $result = $this->curlAnyday->request();

                    if ($result['errorCode'] == 0 && isset($result['transactionId'])) {
                        $payment->setAdditionalInformation(
                            'quote_' . $payment->getOrder()->getQuoteId(),
                            [
                                self::NAME_URL => 'https://my.anyday.io' . $result['authorizeUrl'],
                                self::NAME_TRANSACTION => $result['transactionId'],
                                self::NAME_QUOTE => (int)$payment->getOrder()->getQuoteId(),
                                self::NAME_AMOUNT => (double)$payment->getOrder()->getGrandTotal()
                            ]
                        );
                    } else {
                        $errorText = __('ANYDAY payment Error.');
                        if (isset($result['errorMessage'])) {
                            $errorText = __($result['errorMessage']);
                        }
                        return $this->createResult(false, [$errorText]);
                    }
                } catch (NoData $exception) {
                    return $this->createResult(false, [$exception->getMessage()]);
                }

                $this->serviceAnydayOrder->setOrderStatus(
                    $order,
                    $this->config->getConfigValue(Config::PATH_TO_NEW_ORDER_STATUS),
                    false
                );
            }
            return $this->createResult(true, [__('ok')]);
        }

        return $this->createResult(false, [__('No Find order')]);
    }

    /**
     * Validate order is validate
     *
     * @param Payment $payment
     * @return bool
     */
    private function isNotVerifed(Payment $payment): bool
    {
        $stepDataBilling = $payment->getAdditionalInformation('quote_' . $payment->getOrder()->getQuoteId());
        if ($stepDataBilling) {
            if (isset($stepDataBilling[self::NAME_AMOUNT]) &&
                (double)$stepDataBilling[self::NAME_AMOUNT] == (double)$payment->getOrder()->getGrandTotal()) {
                return false;
            }
        }
        return true;
    }

    /**
     * Factory method
     *
     * @param bool $isValid
     * @param array $fails
     * @param array $errorCodes
     * @return ResultInterface
     */
    protected function createResult(bool $isValid, array $fails = [], array $errorCodes = []): ResultInterface
    {
        return $this->resultInterfaceFactory->create(
            [
                'isValid' => (bool)$isValid,
                'failsDescription' => $fails,
                'errorCodes' => $errorCodes
            ]
        );
    }
}
