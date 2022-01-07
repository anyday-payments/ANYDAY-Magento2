<?php
declare(strict_types=1);

namespace Anyday\Payment\Test\Unit\Block\Checkout\Cart;

use Anyday\Payment\Api\Data\Anydaytag\SettingsInterface;
use Anyday\Payment\Block\Checkout\Cart\Pricetag;
use Anyday\Payment\Service\Settings\Config;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\Registry;
use Anyday\Payment\Lib\Serialize\Serializer\JsonHexTag;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class PricetagTest extends TestCase
{
    /**
     * @var ObjectManagerHelper
     */
    private $objectManagerHelper;

    /**
     * @var Config|MockObject
     */
    private $configMock;

    /**
     * @var Pricetag
     */
    private $model;

    /**
     * @var Registry|MockObject
     */
    private $registryMock;

    /**
     * @var JsonHexTag
     */
    private $json;

    protected function setUp(): void
    {
        $this->objectManagerHelper = new ObjectManagerHelper($this);

        $this->json = $this->objectManagerHelper->getObject(JsonHexTag::class);

        $this->configMock = $this->getMockBuilder(Config::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->registryMock = $this->getMockBuilder(Registry::class)
            ->disableOriginalConstructor()
            ->getMock();

        $context = $this->objectManagerHelper->getObject(
            Context::class
        );

        $this->model = $this->objectManagerHelper->getObject(
            Pricetag::class,
            [
                'config'            => $this->configMock,
                'context'           => $context
            ]
        );
    }

    public function testIsEnabled()
    {
        $this->configMock->expects($this->any())
            ->method('getConfigValue')
            ->with(SettingsInterface::PATH_ENABLE_TAG_CART)
            ->willReturn(true);

        $this->configMock->expects($this->any())
            ->method('isTagModuleEnable')
            ->willReturn(true);

        $resultTestTrue = $this->model->isEnabled();

        $this->assertEquals(
            true,
            $resultTestTrue
        );
    }

    public function testIsDisableModule()
    {
        $this->configMock->expects($this->any())
            ->method('getConfigValue')
            ->with(SettingsInterface::PATH_ENABLE_TAG_CART)
            ->willReturn(true);

        $this->configMock->expects($this->any())
            ->method('isTagModuleEnable')
            ->willReturn(false);

        $resultTestTrue = $this->model->isEnabled();

        $this->assertEquals(
            false,
            $resultTestTrue
        );
    }

    public function testIsDisableProductPage()
    {
        $this->configMock->expects($this->any())
            ->method('getConfigValue')
            ->with(SettingsInterface::PATH_ENABLE_TAG_CART)
            ->willReturn(false);

        $this->configMock->expects($this->any())
            ->method('isTagModuleEnable')
            ->willReturn(true);

        $resultTestTrue = $this->model->isEnabled();

        $this->assertEquals(
            false,
            $resultTestTrue
        );
    }

    public function testGetSelectTagStyle()
    {
        $this->assertEquals(
            Pricetag::SELECT_TAG_STYLE,
            $this->model->getSelectTagStyle()
        );
    }

    public function testGetInlineCss()
    {
        $this->configMock->expects($this->any())
            ->method('getConfigValue')
            ->with(SettingsInterface::PATH_TO_INLINECSS_CART)
            ->willReturn('display: block');

        $resultTestTrue = $this->model->getInlineCss();

        $this->assertEquals(
            "jQuery('.adtag-item').css('display','block');\n",
            $resultTestTrue
        );
    }

    public function testGetInlineCssNoCss()
    {
        $this->configMock->expects($this->any())
            ->method('getConfigValue')
            ->with(SettingsInterface::PATH_TO_INLINECSS_CART)
            ->willReturn('');

        $resultTestTrue = $this->model->getInlineCss();

        $this->assertEquals(
            "",
            $resultTestTrue
        );
    }

    public function testGetInlineCssErrorCss()
    {
        $this->configMock->expects($this->any())
            ->method('getConfigValue')
            ->with(SettingsInterface::PATH_TO_INLINECSS_CART)
            ->willReturn('display');

        $resultTestTrue = $this->model->getInlineCss();

        $this->assertEquals(
            "",
            $resultTestTrue
        );
    }

    public function testGetPrice()
    {
        $this->assertEquals(
            (float)0.0,
            $this->model->getPrice()
        );
    }

    public function testGetTagCode()
    {
        $this->configMock->expects($this->any())
            ->method('getTagToken')
            ->willReturn('xxxxxxxx');

        $resultTestTrue = $this->model->getTagCode();

        $this->assertEquals(
            'xxxxxxxx',
            $resultTestTrue
        );
    }

    public function testGetCurrency()
    {
        $this->configMock->expects($this->any())
            ->method('getCurrencyCode')
            ->willReturn('DKK');

        $this->assertEquals(
            'DKK',
            $this->model->getCurrency()
        );
    }

    public function testGetNameSelectElement()
    {
        $this->configMock->expects($this->any())
            ->method('getConfigValue')
            ->with(SettingsInterface::PATH_TO_SELECT_TAG_ELEMENT_CART)
            ->willReturn('test_class');

        $this->assertEquals(
            'test_class',
            $this->model->getNameSelectElement()
        );
    }
}
