<?php
declare(strict_types=1);

namespace Anyday\PaymentAndTrack\Block\Adminhtml;

use Anyday\PaymentAndTrack\Service\Settings\Config;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\View\Element\Template;

class Abstractpricetag extends Template
{
    const SELECT_TAG_STYLE  = '.adtag-item';

    /**
     * @var Json
     */
    protected $jsonHexTagSerializer;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @param Template\Context $context
     * @param Json $jsonHexTagSerializer
     * @param Config $config
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Json $jsonHexTagSerializer,
        Config $config,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->jsonHexTagSerializer = $jsonHexTagSerializer;
        $this->config               = $config;
    }

    /**
     * Return js code to knockout
     *
     * @param string $style
     * @param string $selector
     * @return string| string[] | null
     */
    protected function getInlineCssCode(string $style, string $selector)
    {
        $stylePairs = array_filter(array_map('trim', explode(';', $style)));
        if (!$stylePairs || !$selector) {
            throw new \InvalidArgumentException('Invalid style data given');
        }
        $elementVariable = "jQuery('.adtag-item')";

        /** @var string[] $styles */
        $stylesAssignments = '';
        foreach ($stylePairs as $stylePair) {
            $exploded = array_map('trim', explode(':', $stylePair));
            if (count($exploded) < 2) {
                $stylesAssignments = '';
                break;
            }
            //Converting to camelCase
            $styleAttribute = lcfirst(str_replace(
                ' ',
                '',
                ucwords(str_replace('-', ' ', $exploded[0]))
            ));
            if (count($exploded) > 2) {
                //For cases when ":" is encountered in the style's value.
                $exploded[1] = join('', array_slice($exploded, 1));
            }
            $styleValue = str_replace('\'', '\\\'', trim($exploded[1]));
            $stylesAssignments .= "$elementVariable.css('$styleAttribute','$styleValue');\n";
        }

        return  $stylesAssignments;
    }
}
