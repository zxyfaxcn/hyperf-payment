<?php
declare(strict_types = 1);
namespace Hyperf\Payment\Gateway\Wxpay;

use Hyperf\Contract\ConfigInterface;
use Hyperf\Payment\Helper\Str;
use Hyperf\Payment\HttpRequest;
use Psr\Log\LoggerInterface;

abstract class BaseWxPay
{
    use HttpRequest;
    const NONCE_LEN = 32;// 随机字符串长度

    const REQ_SUC = 'SUCCESS';

    const SIGN_TYPE_MD5 = 'MD5';

    const SIGN_TYPE_SHA = 'HMAC-SHA256';

    /**
     * @var string
     */
    private $prefix;

    /**
     * @var string
     */
    protected $gatewayUrl = '';

    /**
     * @var string
     */
    protected $merKey = '';

    /**
     * @var string
     */
    private $sandboxKey = '';

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
     * 设置加密方式
     * @var string
     */
    protected $signType = '';

    /**
     * 请求方法的名称
     * @var string
     */
    protected $methodName = '';

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

    private function initialize()
    {
        $this->prefix    = 'payment.wx.options';
        $this->isSandbox = $this->getConfig('use_sandbox', false);
        $this->useBackup = $this->getConfig('use_backup', false);
        $this->returnRaw = $this->getConfig('return_raw', false);
        $this->merKey    = $this->getConfig('md5_key', '');
        $this->signType  = $this->getConfig()->get('sign_type', '');
        $this->nonceStr  = Str::getNonceStr(self::NONCE_LEN);

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

    protected function getSignKey()
    {
        $method = 'pay/getsignkey';
        //TODO 配置签名
    }

    /**
     * @param string $key
     * @param        $default
     *
     * @return mixed
     */
    private function getConfig(string $key, $default)
    {
        return $this->config->get($this->prefix . $key, $default);
    }
}
