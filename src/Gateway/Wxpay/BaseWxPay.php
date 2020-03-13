<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://doc.hyperf.io
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf-cloud/hyperf/blob/master/LICENSE
 */

namespace Hyperf\Payment\Gateway\Wxpay;

use Hyperf\Contract\ConfigInterface;
use Hyperf\Payment\Exception\GatewayException;
use Hyperf\Payment\Helper\Str;
use Hyperf\Payment\Helpers\Arr;
use Hyperf\Payment\Helpers\DataParser;
use Hyperf\Payment\HttpRequest;
use Hyperf\Payment\Payment;
use Psr\Log\LoggerInterface;

abstract class BaseWxPay
{
    use HttpRequest;

    const NONCE_LEN = 32; // 随机字符串长度

    const REQ_SUC = 'SUCCESS';

    const SIGN_TYPE_MD5 = 'MD5';

    const SIGN_TYPE_SHA = 'HMAC-SHA256';

    /**
     * @var string
     */
    protected $gatewayUrl = '';

    /**
     * @var string
     */
    protected $merKey = '';

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
    protected $nonceStr = '';

    /**
     * @var bool
     */
    protected $useBackup = false;

    /**
     * 设置加密方式.
     * @var string
     */
    protected $signType = '';

    /**
     * 请求方法的名称.
     * @var string
     */
    protected $methodName = '';

    /**
     * @var string
     */
    private $prefix;

    /**
     * @var string
     */
    private $sandboxKey = '';

    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * @var null|LoggerInterface
     */
    private $logger;

    public function __construct(ConfigInterface $config, ?LoggerInterface $logger = null)
    {
        $this->config = $config;
        $this->logger = $logger;
    }

    /**
     * 检查微信返回的数据是否被篡改过.
     * @throws GatewayException
     * @return bool
     */
    protected function verifySign(array $retData)
    {
        try {
            $retSign = $retData['sign'];
            $values = Arr::removeKeys($retData, ['sign', 'sign_type']);
            $values = Arr::paraFilter($values);
            $values = Arr::arraySort($values);
            $signStr = Arr::createLinkstring($values);
        } catch (\Exception $e) {
            throw new GatewayException('wechat verify sign generate str get error', Payment::SIGN_ERR);
        }

        $signStr .= '&key=' . $this->merKey;
        switch ($this->signType) {
            case self::SIGN_TYPE_MD5:
                $sign = md5($signStr);
                break;
            case self::SIGN_TYPE_SHA:
                $sign = hash_hmac('sha256', $signStr, $this->merKey);
                break;
            default:
                $sign = '';
        }
        return strtoupper($sign) === $retSign;
    }

    /**
     * 获取证书参数.
     * @return array
     */
    protected function getCertOptions()
    {
        ! defined('BASE_PATH') && define('BASE_PATH', dirname(__DIR__, 1));
        return [
            'cert' => $this->getConfig('app_cert_pem', ''),
            'ssl_key' => $this->getConfig('app_key_pem', ''),
            'verify' => BASE_PATH . DIRECTORY_SEPARATOR . 'cert' . DIRECTORY_SEPARATOR . 'wx_cacert.pem',
        ];
    }

    /**
     * 获取签名.
     * @return mixed
     */
    protected function getSignKey()
    {
        $method = 'pay/getsignkey';
        try {
            $resArr = $this->requestWXApi($method, []);

            return $resArr['sandbox_signkey'];
        } catch (GatewayException $e) {
            throw $e;
        }
    }

    /**
     * 获取微信的错误信息.
     * @param mixed $resArr
     * @return string
     */
    protected function getErrorMsg($resArr)
    {
        if (! is_array($resArr)) {
            return 'not array';
        }
        return isset($resArr['retmsg']) ? $resArr['retmsg'] : (isset($resArr['return_msg']) ? $resArr['return_msg'] : 'error');
    }

    /**
     * 请求微信支付的api.
     *
     * @return array|false|mixed|string
     */
    protected function requestWXApi(string $method, array $requestParams)
    {
        $this->methodName = $method;
        try {
            $xmlData = $this->buildParams($requestParams);
            $url = sprintf($this->gatewayUrl, $method);

            $this->setOptions($this->getCertOptions());
            $resXml = $this->postXML($url, $xmlData);
            if (in_array($method, ['pay/downloadbill', 'pay/downloadfundflow'])) {
                return $resXml;
            }

            $resArr = DataParser::toArray($resXml);
            if (! is_array($resArr) || $resArr['return_code'] !== self::REQ_SUC) {
                throw new GatewayException($this->getErrorMsg($resArr), Payment::GATEWAY_REFUSE, $resArr);
            }
            if (isset($resArr['result_code']) && $resArr['result_code'] !== self::REQ_SUC) {
                throw new GatewayException(sprintf('code:%d, desc:%s', $resArr['err_code'], $resArr['err_code_des']), Payment::GATEWAY_CHECK_FAILED, $resArr);
            }

            if (isset($resArr['sign']) && $this->verifySign($resArr) === false) {
                throw new GatewayException('check return data sign failed', Payment::SIGN_ERR, $resArr);
            }

            return $resArr;
        } catch (GatewayException $e) {
            throw $e;
        }
    }

    /**
     * 生成请求参数.
     * @throws GatewayException
     * @return string
     */
    protected function buildParams(array $requestParams = [])
    {
        $params = [
            'appid' => $this->getConfig('app_id', ''),
            'sub_appid' => $this->getConfig('sub_appid', ''),
            'mch_id' => $this->getConfig('mch_id', ''),
            'sub_mch_id' => $this->getConfig('sub_mch_id', ''),
            'nonce_str' => $this->nonceStr,
            'sign_type' => $this->signType,
        ];
        $params = $this->changeKeyName($params);

        if (! empty($requestParams)) {
            $selfParams = $this->getSelfParams($requestParams);

            if (is_array($selfParams) && ! empty($selfParams)) {
                $params = array_merge($params, $selfParams);
            }
        }

        $params = Arr::paraFilter($params);
        $params = Arr::arraySort($params);

        try {
            $signStr = Arr::createLinkstring($params);
            $params['sign'] = $this->makeSign($signStr);
        } catch (\Exception $e) {
            throw new GatewayException($e->getMessage(), Payment::PARAMS_ERR);
        }

        $xmlData = DataParser::toXml($params);
        if ($xmlData === false) {
            throw new GatewayException('error generating xml', Payment::FORMAT_DATA_ERR);
        }

        return $xmlData;
    }

    /**
     * 签名算法实现  便于后期扩展微信不同的加密方式.
     * @throws GatewayException
     * @return string
     */
    protected function makeSign(string $signStr)
    {
        try {
            switch ($this->signType) {
                case self::SIGN_TYPE_MD5:
                    $signStr .= '&key=' . $this->merKey;
                    $sign = md5($signStr);
                    break;
                case self::SIGN_TYPE_SHA:
                    $signStr .= '&key=' . $this->merKey;
                    $sign = strtoupper(hash_hmac('sha256', $signStr, $this->merKey));
                    break;
                default:
                    throw new GatewayException(sprintf('[%s] sign type not support', $this->signType), Payment::PARAMS_ERR);
            }
        } catch (GatewayException $e) {
            throw $e;
        }

        return strtoupper($sign);
    }

    /**
     * 修改关键key的名字.
     * @return array
     */
    protected function changeKeyName(array $params)
    {
        $changeMap = [
            'mmpaymkttransfers/promotion/transfers',
            'mmpaymkttransfers/sendredpack',
        ];

        if (! in_array($this->methodName, $changeMap)) {
            return $params;
        }

        if ($this->methodName === 'mmpaymkttransfers/promotion/transfers') {
            $params['mch_appid'] = $params['appid'];
            $params['mchid'] = $params['mch_id'];
            unset($params['appid']);
        } elseif ($this->methodName === 'mmpaymkttransfers/sendredpack') {
            unset($params['appid']);
            $params['wxappid'] = $this->getConfig('app_id', '');
        }

        return $params;
    }

    protected function setGatewayUrl(string $gatewayUrl)
    {
        $this->gatewayUrl = $gatewayUrl;
    }

    /**
     * @return mixed
     */
    abstract protected function getSelfParams(array $requestParams);

    /**
     * @param $default
     *
     * @return mixed
     */
    protected function getConfig(string $key, $default)
    {
        return $this->config->get($this->prefix . $key, $default);
    }

    private function initialize()
    {
        $this->prefix = 'payment.wx.options';
        $this->isSandbox = $this->getConfig('use_sandbox', false);
        $this->useBackup = $this->getConfig('use_backup', false);
        $this->returnRaw = $this->getConfig('return_raw', false);
        $this->merKey = $this->getConfig('md5_key', '');
        $this->signType = $this->getConfig()->get('sign_type', '');
        $this->nonceStr = Str::getNonceStr(self::NONCE_LEN);

        // 初始 微信网关地址
        $this->gatewayUrl = 'https://api.mch.weixin.qq.com/%s';
        if ($this->isSandbox) {
            $this->gatewayUrl = 'https://api.mch.weixin.qq.com/sandboxnew/%s';
        } elseif ($this->useBackup) {
            $this->gatewayUrl = 'https://api2.mch.weixin.qq.com/%s'; // 灾备地址
        }

        // 如果是沙盒模式，更换密钥
        if ($this->isSandbox && empty($this->sandboxKey)) {
            $this->sandboxKey = $this->getSignKey();
            //$this->sandboxKey = 'c15772692e55c8db69b40d1cb8e6f627';
            $this->merKey = $this->sandboxKey;
        }
    }
}
