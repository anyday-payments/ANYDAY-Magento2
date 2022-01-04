<?php

namespace Anyday\PaymentAndTrack\Lib\Serialize\Serializer;

use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\Serialize\Serializer\Json;

/**
 * Serialize data to JSON with the JSON_HEX_TAG option enabled
 * (All < and > are converted to \u003C and \u003E),
 * unserialize JSON encoded data
 *
 * @api
 * @since 100.2.0
 */
class JsonHexTag extends Json implements SerializerInterface
{
    /**
     * @inheritDoc
     * @since 100.2.0
     */
    public function serialize($data): string
    {
        $result = json_encode($data, JSON_HEX_TAG);
        if (false === $result) {
            throw new \InvalidArgumentException('Unable to serialize value.');
        }
        return $result;
    }
}
