<?php
declare(strict_types=1);

namespace Anyday\Payment\Gateway\Validator;

use Anyday\Payment\Service\Settings\Config;
use Exception;
use Anyday\Payment\Lib\Serialize\Serializer\JsonHexTag;
use Magento\Payment\Gateway\Validator\AbstractValidator;
use Magento\Payment\Gateway\Validator\ResultInterface;
use Magento\Payment\Gateway\Validator\ResultInterfaceFactory;
use Magento\Quote\Model\QuoteManagement;

class Availability extends AbstractValidator
{
    const NAME_URL          = 'url';
    const NAME_TRANSACTION  = 'transactionId';
    const NAME_QUOTE        = 'quoteId';
    const NAME_AMOUNT       = 'amount';
    const AVAIBILITYCURRENCY= ['DKK'];

    /**
     * @var Config
     */
    private $config;

    /**
     * @var JsonHexTag
     */
    private $json;

    /**
     * @var QuoteManagement
     */
    private $management;

    /**
     * Availability constructor.
     *
     * @param ResultInterfaceFactory $resultFactory
     * @param Config $config
     * @param JsonHexTag $json
     * @param QuoteManagement $management
     */
    public function __construct(
        ResultInterfaceFactory $resultFactory,
        Config $config,
        JsonHexTag $json,
        QuoteManagement $management
    ) {
        parent::__construct($resultFactory);
        $this->config           = $config;
        $this->json             = $json;
        $this->management       = $management;
    }

    /**
     * Validate Order
     *
     * @param array $validationSubject
     * @return ResultInterface|void
     * @throws Exception
     */
    public function validate(array $validationSubject): ResultInterface
    {
        $currencyCode = $this->config->getCurrencyCode();

        if ($validationSubject) {
            if (in_array($currencyCode, self::AVAIBILITYCURRENCY)) {
                return $this->createResult(
                    true,
                    [__('Gateway rejected the transaction.')]
                );
            }
        }

        return $this->createResult(
            false,
            [__('Currency with code %s not use for Anyday', $currencyCode)]
        );
    }
}
