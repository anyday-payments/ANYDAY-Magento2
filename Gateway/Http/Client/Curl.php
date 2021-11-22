<?php

namespace Anyday\PaymentAndTrack\Gateway\Http\Client;

use Anyday\PaymentAndTrack\Gateway\Exception\NoData;
use Anyday\PaymentAndTrack\Lib\Http\Client\LibCurl;
use Anyday\PaymentAndTrack\Lib\Serialize\Serializer\JsonHexTag;

class Curl
{
    /**
     * @var string
     */
    private $body;

    /**
     * @var string
     */
    private $keyAuthorize;

    /**
     * @var JsonHexTag
     */
    private $json;

    /**
     * @var string
     */
    private $url;

    /**
     * @var LibCurl
     */
    private $clientHttp;

    /**
     * Curl constructor.
     *
     * @param JsonHexTag $json
     * @param LibCurl $clientHttp
     */
    public function __construct(
        JsonHexTag $json,
        LibCurl $clientHttp
    ) {
        $this->json         = $json;
        $this->clientHttp   = $clientHttp;
    }

    /**
     * Create request
     *
     * @param bool $boolDisableBody
     * @return string
     * @throws NoData
     */
    public function request(bool $boolDisableBody = false)
    {
        if ($this->validateRequestData($boolDisableBody)) {
            $headersArray['Content-Type'] = 'application/json';
            if ($this->keyAuthorize) {
                $headersArray['Authorization'] = 'Bearer ' . $this->keyAuthorize;
            }

            $this->clientHttp->setHeaders($headersArray);

            if ($boolDisableBody) {
                $this->clientHttp->get($this->url);
            } else {
                $this->clientHttp->post($this->url, $this->body);
            }
            $response = $this->clientHttp->getBody();

            return $this->json->unserialize($response);
        }

        return '';
    }

    /**
     * Validate request
     *
     * @param bool $boolDisableBody
     * @return bool
     * @throws NoData
     */
    private function validateRequestData(bool $boolDisableBody = false): bool
    {
        if (!$this->url) {
            throw new NoData(__('no found Uri'));
        }

        if (!$this->body && !$boolDisableBody) {
            throw new NoData(__('no found Body Data'));
        }

        return true;
    }

    /**
     * Set body
     *
     * @param string $body
     * @return $this
     */
    public function setBody(string $body): Curl
    {
        $this->body = $body;
        return $this;
    }

    /**
     * Set autorize key
     *
     * @param string $keyAuthorize
     * @return $this
     */
    public function setAuthorization(string $keyAuthorize): Curl
    {
        $this->keyAuthorize = $keyAuthorize;
        $this->setOption(CURLOPT_SSL_VERIFYHOST,false);
        $this->setOption(CURLOPT_SSL_VERIFYPEER,false);
        return $this;
    }

    /**
     * Set Url request
     *
     * @param string $url
     * @return $this
     */
    public function setUrl(string $url): Curl
    {
        $this->url = $url;
        return $this;
    }

    public function setOption($key, $value) {
        $this->clientHttp->setOption($key, $value);
    }
}
