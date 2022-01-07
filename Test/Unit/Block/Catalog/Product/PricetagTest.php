<?php
declare(strict_types=1);

namespace Anyday\Payment\Test\Unit\Block\Catalog\Product;

use Anyday\Payment\Api\Data\Anydaytag\SettingsInterface;
use Anyday\Payment\Block\Catalog\Product\Pricetag;
use Anyday\Payment\Service\Settings\Config;
use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Model\Product;
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
     * @var Product|MockObject
     */
    private $productMock;

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
            Context::class,
            [
                'registry'   => $this->registryMock
            ]
        );

        $this->productMock = $this->getMockBuilder(\Magento\Catalog\Model\Product::class)
            ->disableOriginalConstructor()
            ->getMock();

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
            ->with(SettingsInterface::PATH_ENABLE_TAG_PRODUCT)
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
            ->with(SettingsInterface::PATH_ENABLE_TAG_PRODUCT)
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
            ->with(SettingsInterface::PATH_ENABLE_TAG_PRODUCT)
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

    public function testGetInlineCss()
    {
        $this->configMock->expects($this->any())
            ->method('getConfigValue')
            ->with(SettingsInterface::PATH_TO_INLINECSS_PRODUCT)
            ->willReturn('test css');

        $resultTestTrue = $this->model->getInlineCss();

        $this->assertEquals(
            'test css',
            $resultTestTrue
        );
    }

    public function testGetPrice()
    {
        $price = $this->model->getPrice();
        $this->registryMock->expects($this->any())
            ->method('registry')
            ->with('product')
            ->willReturn($this->productMock);

        $this->productMock->expects($this->any())
            ->method('getFinalPrice')
            ->willReturn((float)20.22);

        $this->assertEquals(
            (float)20.22,
            $price
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
            ->with(SettingsInterface::PATH_TO_SELECT_TAG_ELEMENT_PRODUCT)
            ->willReturn('test_class');

        $this->assertEquals(
            'test_class',
            $this->model->getNameSelectElement()
        );
    }
}
