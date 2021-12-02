<?php
declare(strict_types=1);

namespace Anyday\PaymentAndTrack\Test\Unit\Gateway\Command;

use Anyday\PaymentAndTrack\Gateway\Command\InitializeStrategyCommand;
use Anyday\PaymentAndTrack\Gateway\Http\Client\Curl;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Payment;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Magento\Payment\Gateway\Data\PaymentDataObject;

class InitializeStrategyCommandTest extends TestCase
{
    /**
     * @var ObjectManagerHelper
     */
    private $objectManagerHelper;

    /**
     * @var MockObject
     */
    private $paymentDataObjectMock;

    /**
     * @var Payment|MockObject
     */
    private $paymentMock;

    /**
     * @var Order|MockObject
     */
    private $orderMock;

    /**
     * @var InitializeStrategyCommand
     */
    private $model;

    /**
     * @var Curl|MockObject
     */
    private $curlAnydayMock;

    protected function setUp(): void
    {
        $this->objectManagerHelper = new ObjectManagerHelper($this);

        $this->paymentDataObjectMock = $this->getMockBuilder(
            PaymentDataObject::class
        )->disableOriginalConstructor()
            ->getMock();
        $this->paymentMock = $this->getMockBuilder(Payment::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->orderMock = $this->getMockBuilder(Order::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->curlAnydayMock = $this->getMockBuilder(Curl::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->initializeCurl();

        $this->model = $this->objectManagerHelper->getObject(
            InitializeStrategyCommand::class,
            [
                'curlAnyday'
            ]
        );
    }

    public function testExecute()
    {
        $quoteId = 2;
        $this->paymentDataObjectMock->expects($this->any())
            ->method('getPayment')
            ->willReturn($this->paymentMock);

        $this->paymentMock->expects($this->any())
            ->method('getOrder')
            ->willReturn($this->orderMock);

        $this->paymentMock->expects($this->any())
            ->method('getAdditionalInformation')
            ->willReturn('');

        $this->orderMock->expects($this->any())
            ->method('getQuoteId')
            ->willReturn($quoteId);

        $this->assertEquals(
            true,
            $this->model->execute(['payment'=>$this->paymentDataObjectMock])
        );
    }

    private function initializeCurl()
    {
        $this->curlAnydayMock->expects($this->any())
            ->method('setBody')
            ->willReturnSelf();
        $this->curlAnydayMock->expects($this->any())
            ->method('setUrl')
            ->willReturnSelf();
        $this->curlAnydayMock->expects($this->any())
            ->method('setAuthorization')
            ->willReturnSelf();
    }
}
