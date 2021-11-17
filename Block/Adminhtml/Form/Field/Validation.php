<?php
declare(strict_types=1);

namespace Anyday\PaymentAndTrack\Block\Adminhtml\Form\Field;

use Anyday\PaymentAndTrack\Api\Settings\AnydayendpointInterface;
use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Exception\LocalizedException;

class Validation extends Field
{
    /**
     * @var AnydayendpointInterface
     */
    private $anydayendpointInterface;

    /**
     * Validation constructor.
     *
     * @param Context $context
     * @param AnydayendpointInterface $anydayendpointInterface
     * @param array $data
     */
    public function __construct(
        Context $context,
        AnydayendpointInterface $anydayendpointInterface,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->anydayendpointInterface   = $anydayendpointInterface;
    }

    /**
     * Return html string
     *
     * @param AbstractElement $element
     * @return string
     * @throws LocalizedException
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        if ($element) {
            $title = __('Validate Credentials');
            $envId = 'select-groups-braintree-section-groups-braintree-groups-braintree-'
                . 'required-fields-environment-value';

            $endpoint = $this->anydayendpointInterface->getValidateCredentialsEndpoint();

            // @codingStandardsIgnoreStart
            $html = <<<TEXT
            <button
                type="button"
                title="{$title}"
                class="button"
                onclick="anydayValidator.call(this, '{$endpoint}' , '{$envId}')">
                <span>{$title}</span>
            </button>
TEXT;
            // @codingStandardsIgnoreEnd

            return $html;
        }

        return '';
    }
}
