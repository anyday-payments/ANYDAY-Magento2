<?php
//declare(strict_types=1);

namespace Anyday\PaymentAndTrack\Test\Unit\Block\Adminhtml\Config;

use Anyday\PaymentAndTrack\Block\Adminhtml\Config\Store;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;
use PHPUnit\Framework\TestCase;
use Magento\Framework\App\RequestInterface;
use Anyday\PaymentAndTrack\Lib\Serialize\Serializer\JsonHexTag;

class StoreTest extends TestCase
{
    /**
     * @var ObjectManagerHelper
     */
    private $objectManagerHelper;

    /**
     * @var RequestInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $requestMock;

    /**
     * @var Context
     */
    private $context;
    /**
     * @var Store
     */
    private $model;

    /**
     * @var JsonHexTag
     */
    private $json;

    protected function setUp(): void
    {
        $this->objectManagerHelper = new ObjectManagerHelper($this);

        $this->json = $this->objectManagerHelper->getObject(Json::class);

        $this->requestMock = $this->getMockBuilder(RequestInterface::class)
            ->getMock();

        $context = $this->objectManagerHelper->getObject(
            Context::class,
            [
                'request'   => $this->requestMock
            ]
        );

        $this->model = $this->objectManagerHelper->getObject(
            Store::class,
            [
                'context'   => $context,
                'json'      => $this->json
            ]
        );
    }

    public function testGetStoreJson()
    {
        $this->requestMock->expects($this->at(0))
            ->method('getParam')
            ->with('website', 0)
            ->willReturn(2);

        $result = $this->model->getStoreJson();

        $this->requestMock->expects($this->at(0))
            ->method('getParam')
            ->with('website', 0)
            ->willReturn(0);

        $this->requestMock->expects($this->at(1))
            ->method('getParam')
            ->with('store')
            ->willReturn(2);

        $result2 = $this->model->getStoreJson();

        $this->assertEquals(
            $this->json->serialize(
                [
                    'type' => Store::NAME_WEBSITE,
                    'id'    => 2
                ]
            ),
            $result
        );

        $this->assertEquals(
            $this->json->serialize(
                [
                    'type' => Store::NAME_STORE,
                    'id'    => 2
                ]
            ),
            $result2
        );
    }
}
