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
use Magento\Sales\Api\TransactionRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Sales\Api\Data\TransactionInterface;

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
     * @var TransactionRepositoryInterface
     */
    protected $repository;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * Cancel constructor.
     *
     * @param Context $context
     * @param Session $checkoutSession
     * @param OrderRepositoryInterface $orderRepository
     * @param QuoteRepository $quoteRepository
     * @param TransactionRepositoryInterface $repository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        Context $context,
        Session $checkoutSession,
        OrderRepositoryInterface $orderRepository,
        QuoteRepository $quoteRepository,
        TransactionRepositoryInterface $repository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        parent::__construct($context);
        $this->checkoutSession       = $checkoutSession;
        $this->orderRepository       = $orderRepository;
        $this->quoteRepository       = $quoteRepository;
        $this->repository            = $repository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
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
            $this->updateCancelledTransaction($order);
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

    /**
     * Remove all parent transaction from current order
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @return void
     */
    private function updateCancelledTransaction($order)
    {
        $this->searchCriteriaBuilder->addFilter('order_id', $order->getId());
        $list = $this->repository->getList(
            $this->searchCriteriaBuilder->create()
        );
        foreach ($list as $txn) {
            if ($txn->getTxnType() == TransactionInterface::TYPE_VOID) {
                $txn->setParentTxnId(null);
                $this->repository->save($txn);
            }
        }
    }
}
