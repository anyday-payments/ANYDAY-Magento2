<?php
declare(strict_types=1);

namespace Anyday\Payment\Plugins\Block\Adminhtml\Order;

use Anyday\Payment\Model\Ui\ConfigProvider;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Phrase;
use Magento\Sales\Api\TransactionRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Payment\Transaction;

class View
{
    /**
     * @var bool
     */
    private static $writeMessage = false;

    /**
     * @var ManagerInterface
     */
    private $messageManager;

    /**
     * @var TransactionRepositoryInterface
     */
    private $transactionRepository;

    /**
     * @var FilterBuilder
     */
    private $filterBuilder;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * View constructor.
     *
     * @param ManagerInterface $messageManager
     * @param TransactionRepositoryInterface $transactionRepository
     * @param FilterBuilder $filterBuilder
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        ManagerInterface $messageManager,
        TransactionRepositoryInterface $transactionRepository,
        FilterBuilder $filterBuilder,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->messageManager           = $messageManager;
        $this->transactionRepository    = $transactionRepository;
        $this->filterBuilder            = $filterBuilder;
        $this->searchCriteriaBuilder    = $searchCriteriaBuilder;
    }

    /**
     * Add order view message
     *
     * @param \Magento\Sales\Block\Adminhtml\Order\View $subject
     * @param Order $result
     * @return Order
     */
    public function afterGetOrder(
        \Magento\Sales\Block\Adminhtml\Order\View $subject,
        Order $result
    ) {
        if ($subject) {
            if ($result->getPayment()->getMethodInstance()->getCode() == ConfigProvider::CODE) {
                if ($this->isPaymentAuthorise($result)) {
                    if ($result->canInvoice()) {
                        $this->writeMessage(
                            __('Payment has been authorized, you can proceed with invoice and capture.')
                        );
                    }
                } elseif (!$result->isCanceled()) {
                    $this->writeMessage(__('Please authorize payment before invoice.'), true);
                }
            }
        }
        return $result;
    }

    /**
     * Write message
     *
     * @param Phrase $message
     * @param bool $typeError
     */
    private function writeMessage(Phrase $message, bool $typeError = false)
    {
        if (!self::$writeMessage) {
            if ($typeError) {
                $this->messageManager->addErrorMessage($message);
            } else {
                $this->messageManager->addNoticeMessage($message);
            }
            self::$writeMessage = true;
        }
    }

    /**
     * Get is payment authorise
     *
     * @param Order $order
     * @return bool
     */
    private function isPaymentAuthorise(Order $order)
    {
        $this->searchCriteriaBuilder->addFilter('order_id', $order->getId());
        $listTransaction = $this->transactionRepository->getList($this->searchCriteriaBuilder->create())->getItems();
        foreach ($listTransaction as $oneTransaction) {
            /** @var $oneTransaction Transaction */
            if ($oneTransaction->getTxnType() == 'order') {
                return true;
            }
        }

        return false;
    }
}
