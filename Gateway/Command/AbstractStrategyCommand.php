<?php
declare(strict_types=1);

namespace Anyday\Payment\Gateway\Command;

use Anyday\Payment\Gateway\Http\Client\Curl;
use Anyday\Payment\Service\Settings\Config;
use Magento\Checkout\Model\Session;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Registry;
use Anyday\Payment\Lib\Serialize\Serializer\JsonHexTag;
use Magento\Payment\Gateway\CommandInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Api\TransactionRepositoryInterface;
use Anyday\Payment\Service\Anyday\Transaction;

abstract class AbstractStrategyCommand implements CommandInterface
{
    /**
     * @var Session
     */
    protected $checkoutSession;

    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var JsonHexTag
     */
    protected $json;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var TransactionRepositoryInterface
     */
    protected $repository;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var Curl
     */
    protected $curlAnyday;

    /**
     * @var Transaction
     */
    protected $transaction;

    /**
     * AbstractStrategyCommand constructor.
     *
     * @param OrderRepositoryInterface $orderRepository
     * @param JsonHexTag $json
     * @param Config $config
     * @param TransactionRepositoryInterface $repository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param Registry $registry
     * @param Curl $curlAnyday
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository,
        JsonHexTag $json,
        Config $config,
        TransactionRepositoryInterface $repository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        Registry $registry,
        Curl $curlAnyday,
        Transaction $transaction
    ) {
        $this->orderRepository          = $orderRepository;
        $this->json                     = $json;
        $this->config                   = $config;
        $this->repository               = $repository;
        $this->searchCriteriaBuilder    = $searchCriteriaBuilder;
        $this->registry                 = $registry;
        $this->curlAnyday               = $curlAnyday;
        $this->transaction              = $transaction;
    }
}
