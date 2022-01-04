<?php
declare(strict_types=1);

namespace Anyday\PaymentAndTrack\Gateway;

use Anyday\PaymentAndTrack\Api\Payment\AnydayUrlInterface;
use Anyday\PaymentAndTrack\Gateway\Validator\Availability;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Anyday\PaymentAndTrack\Lib\Serialize\Serializer\JsonHexTag;
use Magento\Quote\Model\QuoteIdMaskFactory;
use Magento\Sales\Model\OrderRepository;

class ManagerUrl implements AnydayUrlInterface
{
    /**
     * @var QuoteIdMaskFactory
     */
    private $quoteIdMaskFactory;

    /**
     * @var JsonHexTag
     */
    private $json;

    /**
     * @var OrderRepository
     */
    private $orderRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * ManagerUrl constructor.
     *
     * @param QuoteIdMaskFactory $quoteIdMaskFactory
     * @param JsonHexTag $json
     * @param OrderRepository $orderRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        QuoteIdMaskFactory $quoteIdMaskFactory,
        JsonHexTag $json,
        OrderRepository $orderRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->quoteIdMaskFactory   = $quoteIdMaskFactory;
        $this->json                 = $json;
        $this->orderRepository      = $orderRepository;
        $this->searchCriteriaBuilder= $searchCriteriaBuilder;
    }

    /**
     * Get paymemt url by cart id
     *
     * @param string $cartId
     * @return string
     */
    public function getAnydayPaymentUrl(string $cartId): string
    {
        $quoteIdMask = $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id');
        if ($quoteIdMask->getQuoteId() !== null) {
            $cartId = $quoteIdMask->getQuoteId();
        }
        $this->searchCriteriaBuilder->addFilter('quote_id', $cartId);
        $orderList = $this->orderRepository->getList($this->searchCriteriaBuilder->create())->getItems();

        if (count($orderList)) {
            $order  = array_shift($orderList);
            $anydayData = $order->getPayment()->getAdditionalInformation('quote_' . $cartId);

            if ($anydayData && isset($anydayData[Availability::NAME_URL])) {
                return $this->json->serialize(
                    ['url' => $anydayData[Availability::NAME_URL]]
                );
            }
        }

        return '';
    }
}
