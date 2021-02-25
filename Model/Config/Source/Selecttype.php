<?php
declare(strict_types=1);

namespace Anyday\PaymentAndTrack\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class Selecttype implements ArrayInterface
{

    /**
     * Get Labels to select
     *
     * @return array[]
     */
    public function toOptionArray()
    {
        return [
            [
                'value'=>0, 'label'=>__('Select option')
            ],
            [
                'value'=>1, 'label'=>__('Tag')
            ],
            [
                'value'=>2, 'label'=>__('Name Element')
            ],
            [
                'value'=>3, 'label'=>__('Class Element')
            ]
        ];
    }
}
