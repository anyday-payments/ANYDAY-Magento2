<?php

namespace Anyday\Payment\Controller\Payment;

use Anyday\Payment\Api\Data\Anydaytag\SettingsInterface;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Anyday\Payment\Model\Event\Events;
use Magento\Framework\App\Request\Http;
use Anyday\Payment\Service\Settings\Config;

class Webhook extends Action
{
    /**
     * @var \Anyday\Payment\Model\Event
     */
    protected $event;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    /**
     * @var \Anyday\Payment\Service\Settings\Config
     */
    protected $config;

    /**
     * @param \Magento\Framework\App\Action\Context   $context
     * @param \Anyday\Payment\Model\Event\Events      $event
     * @param \Magento\Framework\App\Request\Http     $request
     * @param \Anyday\Payment\Service\Settings\Config $config
     */
    public function __construct(
        Context $context,
        Events $event,
        Http $request,
        Config $config
    ) {
        $this->event   = $event;
        $this->request = $request;
        $this->config  = $config;
        parent::__construct($context);
    }

    /**
     * @return void
     */
    public function execute()
    {
        $payload = json_decode($this->request->getContent());

        if ($payload->transaction === null || ! $payload->id || !$this->verifiedSignature()) {
            return;
        }

        $this->event->handle($payload);
    }

    /**
     * Checks if the signature is valid. Returns true if valid.
     * @return boolean
     */
    private function verifiedSignature()
    {
        $private    = $this->config->getConfigValue(SettingsInterface::PATH_TO_SECRET_KEY);
        $signature  = $this->request->getHeader('x_anyday_signature');
        if (empty(trim($private))) {
            return false;
        }
        $signedBody = hash_hmac('sha256', $this->request->getContent(), $private);
        if ($signature === $signedBody) {
            return true;
        }
        return false;
    }
}
