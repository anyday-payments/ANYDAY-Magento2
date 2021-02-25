<?php
declare(strict_types=1);

namespace Anyday\PaymentAndTrack\Service\Anyday;

use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Model\OrderRepository;
use Magento\Sales\Model\ResourceModel\Order\Status\Collection;

class Order
{
    /**
     * @var OrderRepository
     */
    private $orderRepository;

    /**
     * @var Collection
     */
    private $collectionStatus;

    /**
     * Order constructor.
     *
     * @param OrderRepository $orderRepository
     * @param Collection $collectionStatus
     */
    public function __construct(
        OrderRepository $orderRepository,
        Collection $collectionStatus
    ) {
        $this->orderRepository  = $orderRepository;
        $this->collectionStatus = $collectionStatus;
    }

    /**
     * Set order status
     *
     * @param \Magento\Sales\Model\Order $order
     * @param string $orderStatus
     * @param bool $saveOrder
     * @throws AlreadyExistsException
     * @throws InputException
     * @throws NoSuchEntityException
     */
    public function setOrderStatus(\Magento\Sales\Model\Order $order, string $orderStatus, bool $saveOrder = true)
    {
        if ($orderState = $this->getOrderStateByStatus($orderStatus)) {
            $order->setState($orderState)->setStatus($orderStatus);
            if ($saveOrder) {
                $this->orderRepository->save($order);
            }
        }
    }

    /**
     * Get order state by status
     *
     * @param string $orderStatus
     * @return false|string
     */
    private function getOrderStateByStatus(string $orderStatus)
    {
        $collectionStatus = $this->collectionStatus->addAttributeToFilter('main_table.status', $orderStatus);
        $collectionStatus->joinStates();
        if ($collectionStatus->count()) {
            $firstItem = $collectionStatus->getFirstItem();
            return $firstItem->getState();
        }

        return false;
    }
}
