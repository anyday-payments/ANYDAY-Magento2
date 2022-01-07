<?php
declare(strict_types=1);

namespace Anyday\Payment\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class Modetype implements OptionSourceInterface
{
    /**
     * Get labels to select
     *
     * @return array[]
     */
    public function toOptionArray()
    {
        return [
            ['value' => 1, 'label' => __('Select Live')],
            ['value' => 0, 'label' => __('Select Test Mode')]
        ];
    }
}
