<?php
declare(strict_types=1);

namespace Anyday\Payment\Gateway\Exception;

use Exception;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;

class NoData extends LocalizedException
{
    /**
     * @param Phrase|null $phrase
     * @param Exception|null $cause
     * @param int $code
     */
    public function __construct(Phrase $phrase = null, Exception $cause = null, $code = 0)
    {
        if ($phrase === null) {
            $phrase = new Phrase(__('No data found'));
        }
        parent::__construct($phrase, $cause, $code);
    }
}
