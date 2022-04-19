<?php
declare(strict_types=1);

namespace Anyday\Payment\Controller\Payment;

use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Quote\Model\QuoteRepository;

class Cancel extends Action
{
    /**
     * @var Session
     */
    private $checkoutSession;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var QuoteRepository
     */
    private $quoteRepository;

    /**
     * Cancel constructor.
     *
     * @param Context $context
     * @param Session $checkoutSession
     * @param OrderRepositoryInterface $orderRepository
     * @param QuoteRepository $quoteRepository
     */
    public function __construct(
        Context $context,
        Session $checkoutSession,
        OrderRepositoryInterface $orderRepository,
        QuoteRepository $quoteRepository
    ) {
        parent::__construct($context);
        $this->checkoutSession  = $checkoutSession;
        $this->orderRepository  = $orderRepository;
        $this->quoteRepository  = $quoteRepository;
    }

    /**
     * Update status order and status order
     *
     * @return ResponseInterface|Redirect|ResultInterface
     */
    public function execute()
    {
        $orderId = $this->checkoutSession->getLastOrderId();
        /** @var Order $order */
        $order = $orderId ? $this->orderRepository->get($orderId) : false;
        if ($order && $order->getId()) {
            $quote = $this->quoteRepository->get($order->getQuoteId());
            $quote->setIsActive(true)->setReservedOrderId(null);
            $this->quoteRepository->save($quote);
            $this->orderRepository->save($order->cancel());
            $this->checkoutSession->replaceQuote($quote);
            $this->messageManager->addSuccessMessage(
                __('Anyday Order have been canceled.')
            );
        } else {
            $this->messageManager->addErrorMessage(
                __('Not Load Order.')
            );
        }

        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('checkout/cart');
    }
}
