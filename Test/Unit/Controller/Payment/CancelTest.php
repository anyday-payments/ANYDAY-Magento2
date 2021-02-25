<?php
declare(strict_types=1);

namespace Anyday\PaymentAndTrack\Test\Unit\Controller\Payment;

use Anyday\PaymentAndTrack\Controller\Payment\Cancel;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CancelTest extends TestCase
{
    /**
     * @var ObjectManagerHelper
     */
    private $objectManagerHelper;

    /**
     * @var Cancel
     */
    private $model;

    /**
     * @var Session|MockObject
     */
    private $checkoutSessionMock;

    /**
     * @var OrderRepositoryInterface|MockObject
     */
    private $orderRepositoryMock;

    /**
     * @var Order|MockObject
     */
    protected $orderMock;

    /**
     * @var ManagerInterface|MockObject
     */
    private $messageManagerMock;
    /**
     * @var Redirect|MockObject
     */
    private $resulRedirect;

    protected function setUp(): void
    {
        $this->objectManagerHelper = new ObjectManagerHelper($this);

        $this->messageManagerMock = $this->getMockBuilder(ManagerInterface::class)
            ->getMock();

        $this->context = $this->createMock(Context::class);

        $resultRedirectFactory = $this->createPartialMock(
            ResultFactory::class,
            ['create']
        );

        $this->checkoutSessionMock = $this->createPartialMock(
            Session::class,
            ['getLastOrderId','restoreQuote']
        );

        $this->orderRepositoryMock = $this->getMockBuilder(OrderRepositoryInterface::class)
            ->getMockForAbstractClass();

        $this->orderMock = $this->getMockBuilder(Order::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->resulRedirect = $this->createMock(Redirect::class);
        $resultRedirectFactory->expects($this->any())
            ->method('create')
            ->with(ResultFactory::TYPE_REDIRECT, [])
            ->willReturn($this->resulRedirect);

        $this->context->expects($this->any())
            ->method('getResultFactory')
            ->willReturn($resultRedirectFactory);

        $this->context->expects($this->any())
            ->method('getMessageManager')
            ->willReturn($this->messageManagerMock);

        $this->model = $this->objectManagerHelper->getObject(
            Cancel::class,
            [
                'checkoutSession'   => $this->checkoutSessionMock,
                'context'           => $this->context,
                'orderRepository'   => $this->orderRepositoryMock
            ]
        );
    }

    public function testExecute()
    {
        $orderId = 2;
        $this->checkoutSessionMock->expects($this->any())
            ->method('getLastOrderId')
            ->willReturn($orderId);

        $this->checkoutSessionMock->expects($this->any())
            ->method('restoreQuote')
            ->willReturn(true);

        $this->orderRepositoryMock->expects($this->any())
            ->method('get')
            ->with($orderId)
            ->willReturn($this->orderMock);

        $this->orderMock->expects($this->any())
            ->method('getId')
            ->willReturn(2);

        $this->orderMock->expects($this->any())
            ->method('cancel')
            ->willReturn($this->orderMock);

        $this->orderRepositoryMock->expects($this->any())
            ->method('save')
            ->with($this->orderMock->cancel())
            ->willReturn($this->orderRepositoryMock);

        $this->messageManagerMock->expects($this->any())
            ->method('addSuccessMessage')
            ->with('ANYDAY Order have been canceled.')
            ->willReturnSelf();

        $this->resulRedirect->expects($this->any())
            ->method('setPath')
            ->with('checkout/cart')
            ->willReturn('checkout/cart');

        $this->assertEquals(
            $this->resulRedirect->setPath('checkout/cart'),
            $this->model->execute()
        );
    }

    public function testExecuteNoSuch()
    {
        $orderId = 2;
        $this->checkoutSessionMock->expects($this->any())
            ->method('getLastOrderId')
            ->willReturn($orderId);

        $this->checkoutSessionMock->expects($this->any())
            ->method('restoreQuote')
            ->willReturn(true);

        $this->orderRepositoryMock->expects($this->any())
            ->method('get')
            ->with($orderId)
            ->willReturn(null);

        $this->orderMock->expects($this->any())
            ->method('getId')
            ->willReturn(2);

        $this->orderMock->expects($this->any())
            ->method('cancel')
            ->willReturn($this->orderMock);

        $this->orderRepositoryMock->expects($this->any())
            ->method('save')
            ->with($this->orderMock->cancel())
            ->willReturn($this->orderRepositoryMock);

        $this->messageManagerMock->expects($this->any())
            ->method('addErrorMessage')
            ->with('Not Load Order.')
            ->willReturnSelf();

        $this->resulRedirect->expects($this->any())
            ->method('setPath')
            ->with('checkout/cart')
            ->willReturn('checkout/cart');

        $this->assertEquals(
            $this->resulRedirect->setPath('checkout/cart'),
            $this->model->execute()
        );
    }
}
