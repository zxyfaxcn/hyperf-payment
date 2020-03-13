<?php
declare(strict_types = 1);
namespace Hyperf\Payment\Gateway\Alipay;

use Hyperf\Contract\ConfigInterface;
use Hyperf\Payment\Exception\GatewayException;
use Hyperf\Payment\Helper\Str;
use Hyperf\Payment\Helper\RsaEncrypt;
use Hyperf\Payment\Helper\Rsa2Encrypt;
use Hyperf\Payment\Helpers\Arr;
use Hyperf\Payment\HttpRequest;
use Hyperf\Payment\Payment;
use Hyperf\Utils\ApplicationContext;
use Psr\Log\LoggerInterface;

abstract class BaseAlipay
{
    use HttpRequest;

    const REQ_SUC = '10000';

    /**
     * @var string
     */
    protected $privateKey = '';

    /**
     * @var string
     */
    protected $publicKey = '';

    /**
     * @var string
     */
    protected $gatewayUrl = '';

    /**
     * @var bool
     */
    protected $isSandbox = false;

    /**
     * @var bool
     */
    protected $returnRaw = false;

    /**
     * @var string
     */
    private $prefix;

    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * @var null|LoggerInterface
     */
    private $logger;

    public function __construct()
    {
        $this->config = ApplicationContext::getContainer()->get(ConfigInterface::class);
        $this->logger = ApplicationContext::getContainer()->get(LoggerInterface::class);
        $this->initialize();
    }

    private function initialize()
    {
        $this->prefix    = 'payment.ali.options';
        $this->isSandbox = $this->getConfig('use_sandbox', false);
        $this->returnRaw = $this->getConfig('return_raw', false);

        $rsaPublicKey = $this->getConfig('ali_public_key', '');
        if ($rsaPublicKey) {
            $this->publicKey = $rsaPublicKey;
        }
        if (empty($this->publicKey)) {
            throw new GatewayException('please set ali public key', Payment::PARAMS_ERR);
        }

        $rsaPrivateKey = $this->getConfig('rsa_private_key', '');
        if ($rsaPrivateKey) {
            $this->privateKey = Str::getRsaKeyValue($rsaPrivateKey, 'private');
        }

        if (empty($this->privateKey)) {
            throw new GatewayException('please set ali private key', Payment::PARAMS_ERR);
        }

        // 初始 支付宝网关地址
        $this->gatewayUrl = 'https://openapi.alipay.com/gateway.do';
        if ($this->isSandbox) {
            $this->gatewayUrl = 'https://openapi.alipaydev.com/gateway.do';
        }
    }

    /**
     * @param string $signType
     * @param string $signStr
     *
     * @return string
     */
    protected function makeSign(string $signType, string $signStr)
    {
        $signType = strtoupper($signType);
        try {
            switch ($signType) {
                case 'RSA':
                    $rsa = new RsaEncrypt($this->privateKey);

                    $sign = $rsa->encrypt($signStr);
                    break;
                case 'RSA2':
                    $rsa = new Rsa2Encrypt($this->privateKey);

                    $sign = $rsa->encrypt($signStr);
                    break;
                default:
                    throw new GatewayException(sprintf('[%s] sign type not support', $signType), Payment::PARAMS_ERR);
            }
        } catch (GatewayException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new GatewayException(sprintf('sign error, sign type is [%s]. msg: [%s]', $signType, $e->getMessage()), Payment::SIGN_ERR);
        }

        return $sign;
    }

    /**
     * @param array  $data
     * @param string $sign
     *
     * @return bool
     * @throws GatewayException
     */
    protected function verifySign(array $data, string $sign)
    {
        $signType = strtoupper($this->getConfig('sign_type', ''));
        $preStr   = json_encode($data, JSON_UNESCAPED_UNICODE);

        try {
            if ($signType === 'RSA') {// 使用RSA
                $rsa = new RsaEncrypt($this->publicKey);
                return $rsa->rsaVerify($preStr, $sign);
            } elseif ($signType === 'RSA2') {// 使用rsa2方式
                $rsa = new Rsa2Encrypt($this->publicKey);
                return $rsa->rsaVerify($preStr, $sign);
            }
            throw new GatewayException(sprintf('[%s] sign type not support', $signType), Payment::PARAMS_ERR);
        } catch (\Exception $e) {
            throw new GatewayException(sprintf('check ali pay sign failed, sign type is [%s]', $signType), Payment::SIGN_ERR, $data);
        }
    }

    /**
     * 针对异步通知的验证签名
     *
     * @param array  $data
     * @param string $sign
     * @param string $signType
     *
     * @return bool
     * @throws GatewayException
     */
    protected function verifySignForASync(array $data, string $sign, string $signType)
    {
        $params = Arr::arraySort($data);

        try {
            $preStr = Arr::createLinkString($params);

            if ($signType === 'RSA') {// 使用RSA
                $rsa = new RsaEncrypt($this->publicKey);
                return $rsa->rsaVerify($preStr, $sign);
            } elseif ($signType === 'RSA2') {// 使用rsa2方式
                $rsa = new Rsa2Encrypt($this->publicKey);
                return $rsa->rsaVerify($preStr, $sign);
            }
            throw new GatewayException(sprintf('[%s] sign type not support', $signType), Payment::PARAMS_ERR);
        } catch (\Exception $e) {
            throw new GatewayException(sprintf('check ali pay sign failed, sign type is [%s]', $signType), Payment::SIGN_ERR, $data);
        }
    }

    /**
     * @param string $method
     * @param array  $requestParams
     *
     * @return array
     * @throws GatewayException
     */
    protected function buildParams(string $method, array $requestParams)
    {
        $bizContent = $this->getBizContent($requestParams);
        $params     = $this->getBaseData($method, $bizContent);

        // 支付宝新版本  需要转码
        foreach ($params as &$value) {
            $value = Str::characet($value, 'utf-8');
        }

        $params = Arr::arraySort($params);
        try {
            $signStr = Arr::createLinkString($params);

            $signType       = $this->getConfig('sign_type', '');
            $params['sign'] = $this->makeSign($signType, $signStr);
        } catch (GatewayException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new GatewayException($e->getMessage(), Payment::PARAMS_ERR);
        }

        return $params;
    }

    /**
     * 获取基础数据
     *
     * @param string $method
     * @param array  $bizContent
     *
     * @return array
     */
    private function getBaseData(string $method, array $bizContent)
    {
        $requestData = [
            'app_id'      => $this->getConfig('app_id', ''),
            'method'      => $method,
            'format'      => 'JSON',
            'return_url'  => $this->getConfig('return_url', ''),
            'charset'     => 'utf-8',
            'sign_type'   => $this->getConfig('sign_type', ''),
            'timestamp'   => date('Y-m-d H:i:s'),
            'version'     => '1.0',
            'notify_url'  => $this->getConfig('notify_url', ''),
            // 'app_auth_token' => '', // 暂时不用
            'biz_content' => json_encode($bizContent, JSON_UNESCAPED_UNICODE),
        ];
        return ArrayUtil::arraySort($requestData);
    }

    /**
     * @param array $requestParams
     *
     * @return mixed
     */
    abstract protected function getBizContent(array $requestParams);

    private function getConfig(string $key, $default)
    {
        return $this->config->get($this->prefix . $key, $default);
    }
}
