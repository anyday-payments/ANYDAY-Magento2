<?php
declare(strict_types=1);

namespace Anyday\PaymentAndTrack\Test\Unit\Controller\Payment;

use Anyday\PaymentAndTrack\Controller\Payment\Succes;
use Anyday\PaymentAndTrack\Gateway\Validator\Availability;
use Anyday\PaymentAndTrack\Service\Settings\Config;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Payment;
use Magento\Sales\Model\Order\Payment\Transaction;
use Magento\Sales\Model\Order\Payment\Transaction\BuilderInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class SuccesTest extends TestCase
{
    /**
     * @var ObjectManagerHelper
     */
    private $objectManagerHelper;

    /**
     * @var Succes
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

    /**
     * @var Payment|MockObject
     */
    private $paymentMock;

    /**
     * @var BuilderInterface|MockObject
     */
    private $builderInterfaceMock;

    /**
     * @var MockObject
     */
    private $orderTransactionMock;

    /**
     * @var Config|MockObject
     */
    private $configMock;

    protected function setUp(): void
    {
        $this->objectManagerHelper = new ObjectManagerHelper($this);

        $this->messageManagerMock = $this->getMockBuilder(ManagerInterface::class)
            ->getMock();

        $this->context = $this->createMock(Context::class);

        $this->orderTransactionMock = $this->createMock(Transaction::class);

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

        $this->builderInterfaceMock = $this->getMockBuilder(BuilderInterface::class)
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

        $this->paymentMock = $this->getMockBuilder( Payment::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->configMock = $this->getMockBuilder(Config::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->model = $this->objectManagerHelper->getObject(
            Succes::class,
            [
                'checkoutSession'   => $this->checkoutSessionMock,
                'context'           => $this->context,
                'orderRepository'   => $this->orderRepositoryMock,
                'builder'           => $this->builderInterfaceMock,
                'configService'     => $this->configMock
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
            ->method('getPayment')
            ->willReturn($this->paymentMock);

        $this->orderRepositoryMock->expects($this->any())
            ->method('save')
            ->with($this->orderMock->cancel())
            ->willReturn($this->orderRepositoryMock);

        $this->messageManagerMock->expects($this->any())
            ->method('addSuccessMessage')
            ->with('Anyday Order have been canceled.')
            ->willReturnSelf();

        $this->resulRedirect->expects($this->any())
            ->method('setPath')
            ->with('checkout/onepage/success')
            ->willReturn('checkout/onepage/success');

        $this->orderMock->expects($this->any())
            ->method('getQuoteId')
            ->willReturn($orderId);

        $this->paymentMock->expects($this->any())
            ->method('getAdditionalInformation')
            ->with('quote_' . $this->orderMock->getQuoteId())
            ->willReturn($this->updatePaymentOrderOk());

        $this->updateBuilderInterface();

        $this->configMock->expects($this->any())
            ->method('getConfigValue')
            ->with(Config::PATH_TO_STATUS_AFTER_PAYMENT)
            ->willReturn('test message');

        $this->paymentMock->expects($this->any())
            ->method('addTransactionCommentsToOrder')
            ->with($this->orderTransactionMock, $this->orderTransactionMock->getTransactionId())
            ->willReturnSelf();

        $this->assertEquals(
            $this->resulRedirect->setPath('checkout/onepage/success'),
            $this->model->execute()
        );
    }

    /**
     * @return string[]
     */
    private function updatePaymentOrderOk()
    {
        return [
            Availability::NAME_TRANSACTION => 'testTransaction'
        ];
    }

    private function updateBuilderInterface()
    {
        $this->builderInterfaceMock->expects($this->any())
            ->method('setPayment')
            ->with($this->paymentMock)
            ->willReturnSelf();

        $this->builderInterfaceMock->expects($this->any())
            ->method('setOrder')
            ->with($this->orderMock)
            ->willReturnSelf();

        $this->builderInterfaceMock->expects($this->any())
            ->method('setTransactionId')
            ->with($this->orderMock->getId())
            ->willReturnSelf();

        $this->builderInterfaceMock->expects($this->any())
            ->method('setAdditionalInformation')
            ->willReturnSelf();

        $this->builderInterfaceMock->expects($this->any())
            ->method('setFailSafe')
            ->willReturnSelf();

        $this->builderInterfaceMock->expects($this->any())
            ->method('build')
            ->willReturn($this->orderTransactionMock);

        $this->orderTransactionMock->expects($this->any())
            ->method('getTransactionId')
            ->willReturn('11111');
    }
}
