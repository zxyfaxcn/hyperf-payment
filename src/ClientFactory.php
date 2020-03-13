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

namespace Hyperf\Payment;

use Hyperf\Payment\Adapter\AlipayFactory;
use Hyperf\Payment\Adapter\WxpayFactory;
use Hyperf\Payment\Contract\PayNotifyInterface;
use Hyperf\Payment\Event\PaymentMeta;
use Hyperf\Payment\Exception\ClassNotFoundException;
use Hyperf\Payment\Exception\GatewayException;
use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * Class ClientFactory.
 * @method array pay(string $channel, array $options) 请求支付
 * @method array refund(array $options) 退款请求
 * @method array tradeQuery(array $options) 交易查询
 * @method array refundQuery(array $options) 退款查询
 * @method array transferQuery(array $options) 转账查询
 * @method array billDownload(array $options) 对账单下载
 * @method array settleDownload(array $options) 结算单下载
 * @method array transfer(array $options) 转账
 * @method array cancel(array $options) 取消交易
 * @method array close(array $options) 关闭交易
 * @method array notify(PayNotifyInterface $options) 异步通知
 */
class ClientFactory
{
    /*----------------支持的渠道-----------------*/
    const ALIPAY = 'Alipay'; // 支付宝

    const WECHAT = 'Wechat'; // 微信

    /*----------------支付宝--------------------*/
    const ALI_CHANNEL_APP = 'app'; // 支付宝 手机app 支付

    const ALI_CHANNEL_WAP = 'wap'; // 支付宝 手机网页 支付

    const ALI_CHANNEL_WEB = 'web'; // 支付宝 PC 网页支付

    const ALI_CHANNEL_QR = 'qr'; // 支付宝 扫码支付

    const ALI_CHANNEL_BAR = 'bar'; // 支付宝 条码支付

    /*----------------微信--------------------*/
    const WX_CHANNEL_APP = 'app'; // 微信 APP 支付

    const WX_CHANNEL_PUB = 'pub'; // 微信 公众账号 支付

    const WX_CHANNEL_QR = 'qr'; // 微信 扫码支付  (可以使用app的帐号，也可以用公众的帐号完成)

    const WX_CHANNEL_BAR = 'bar'; // 微信 刷卡支付，与支付宝的条码支付对应

    const WX_CHANNEL_LITE = 'lite'; // 微信小程序支付

    const WX_CHANNEL_WAP = 'wap'; // 微信wap支付，针对特定用户

    const WX_SETTLE_SINGLE = 'single'; // 单次分账

    const WX_SETTLE_MULTI = 'multi'; // 多次分账

    /**
     * @var AlipayFactory| WxpayFactory
     */
    protected $proxy;

    private $eventDispatcher;

    public function __construct(string $proxy, EventDispatcherInterface $eventDispatcher)
    {
        $this->proxy = $this->make($proxy);
        $this->eventDispatcher = $eventDispatcher;
    }

    public function __call($name, $arguments)
    {
        try {
            if (! method_exists($this->proxy, $name)) {
                throw new \InvalidArgumentException(sprintf('[%s] method is not exist in proxy [%s].', $name, $this->proxy->className()), Payment::PARAMS_ERR);
            }
        } catch (\InvalidArgumentException $e) {
            throw new ClassNotFoundException(sprintf('[%s] class not found.', get_class($this->proxy)), Payment::CLASS_NOT_EXIST);
        } catch (\Exception $e) {
            throw $e;
        }

        try {
            $resonpse = call_user_func_array([$this->proxy, $name], $arguments);
            $this->eventDispatcher->dispatch(new PaymentMeta($this->proxy, $arguments, $resonpse));
            return $resonpse;
        } catch (GatewayException $e) {
            throw $e;
        }
    }

    /**
     * @param string $name
     *
     * @return mixed
     */
    public function make(string $name)
    {
        $class = $this->formatClassName($name);
        if (! class_exists($class)) {
            throw new InvalidArgumentException(sprintf('Proxy [%s] not exists.', $class), Payment::CLASS_NOT_EXIST);
        }
        try {
            $proxy = make($class);
        } catch (\InvalidArgumentException $e) {
            throw $e;
        }
        return $proxy;
    }

    protected function formatClassName(string $proxyName)
    {
        $name = ucfirst(str_replace(['-', '_', ''], '', $proxyName));

        return "Hyperf\\Payment\\Adapter\\{$name}Factory";
    }
}
