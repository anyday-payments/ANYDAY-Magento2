<?php
declare(strict_types=1);

namespace Anyday\Payment\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class Localetype implements OptionSourceInterface
{
    /**
     * Get labels to select
     *
     * @return array[]
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'da', 'label' => __('Danish')],
            ['value' => 'en', 'label' => __('English')]
        ];
    }
}
