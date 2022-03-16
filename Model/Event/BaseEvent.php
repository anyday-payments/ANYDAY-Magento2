<?php

namespace Anyday\Payment\Model\Event;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\FilterBuilder;
use Magento\Sales\Model\Order\Payment\Transaction\Repository as TransactionRepository;

class BaseEvent
{

  /**
   * @var SearchCriteriaBuilder
   */
    protected $searchCriteriaBuilder;

  /**
   * @var FilterBuilder
   */
    protected $filterBuilder;

  /**
   * @var TransactionRepository
   */
    protected $transactionRepository;

  /**
   * @param SearchCriteriaBuilder $searchCriteriaBuilder
   */
    public function __construct(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        FilterBuilder $filterBuilder,
        TransactionRepository $transactionRepository
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
        $this->transactionRepository = $transactionRepository;
    }
}
