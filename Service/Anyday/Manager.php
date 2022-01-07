<?php
declare(strict_types=1);

namespace Anyday\Payment\Service\Anyday;

use Anyday\Payment\Api\Anyday\ManagerInterface;
use Anyday\Payment\Api\Data\Anydaytag\SettingsInterface;
use Anyday\Payment\Block\Adminhtml\Config\Store;
use Anyday\Payment\Gateway\Http\Client\Curl;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Anyday\Payment\Lib\Serialize\Serializer\JsonHexTag;
use Magento\Store\Model\ScopeInterface;

class Manager implements ManagerInterface
{
    /**
     * @var JsonHexTag
     */
    private $json;

    /**
     * @var WriterInterface
     */
    private $writer;

    /**
     * @var Curl
     */
    private $curlAnyday;

    /**
     * @param JsonHexTag $json
     * @param WriterInterface $writer
     * @param Curl $curlAnyday
     */
    public function __construct(
        JsonHexTag $json,
        WriterInterface $writer,
        Curl $curlAnyday
    ) {
        $this->json         = $json;
        $this->writer       = $writer;
        $this->curlAnyday   = $curlAnyday;
    }

    /**
     * Update Credentials
     *
     * @param string $data
     * @return bool|mixed|string
     */
    public function getCredentialsFromAnyday(string $data)
    {
        $data = $this->json->unserialize($data);
        $params['username'] = $data['email'];
        $params['password'] = $data['password'];
        $sendParam = [
            "grant_type" => "password",
            //"username" => "luketestmerchant@anyday.io",
            //"password" => "l@X3#uPN!*RO",
            "userType" => "merchant"
        ];

        $sendParam = array_merge($sendParam, $params);

        $url = 'https://my.anyday.io/api/v1/authentication/login';
        $data_string = json_encode($sendParam);
        $this->curlAnyday->setBody($data_string);
        $this->curlAnyday->setUrl($url);

        $result = $this->curlAnyday->request();
        if (isset($result['errors']) && is_array($result['errors'])) {
            $returnArr['code'] = 'error';
            $returnArr['result'] = implode(' ', $result['errors']);
        } else {
            $returnArr['code'] = 'ok';
            $returnArr['token'] = $result['access_token'];
            $url = 'https://my.anyday.io/api/v1/webshop/mine';

            $this->curlAnyday->setUrl($url);
            $this->curlAnyday->setAuthorization((string)$result['access_token']);
            $result = $this->curlAnyday->request(true);

            if (isset($result['data']) && isset($result['data'][0]) && isset($result['data'][0]['apiKey'])
                    && isset($result['data'][0]['testAPIKey'])) {
                $returnArr['live'] = $result['data'][0]['apiKey'];
                $returnArr['sandbox'] = $result['data'][0]['testAPIKey'];
                $returnArr['priceTagToken'] = $result['data'][0]['priceTagToken'];
                $scope = 'default';
                $scopeId = $data['id'];
                if ($data['type'] == Store::NAME_WEBSITE) {
                    $scope = ScopeInterface::SCOPE_WEBSITES;
                } else {
                    if ((int)$scopeId > 0) {
                        $scope = ScopeInterface::SCOPE_STORES;
                    }
                }
                $this->writer->save(
                    SettingsInterface::PATH_TO_TOKEN_SANDBOX,
                    $result['data'][0]['testAPIKey'],
                    $scope,
                    $scopeId
                );
                $this->writer->save(
                    SettingsInterface::PATH_TO_TOKEN_LIVE,
                    $result['data'][0]['apiKey'],
                    $scope,
                    $scopeId
                );
                $this->writer->save(
                    SettingsInterface::PATH_TO_TAG_TOKEN,
                    $result['data'][0]['priceTagToken'],
                    $scope,
                    $scopeId
                );
            } else {
                $returnArr['code'] = 'error';
                $returnArr['result'] = implode(' ', $result['errors']);
            }
        }

        return $this->json->serialize($returnArr);
    }
}
