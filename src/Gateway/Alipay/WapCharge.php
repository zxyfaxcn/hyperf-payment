<?php
declare(strict_types=1);
namespace Hyperf\Payment\Gateway\Alipay;


use Hyperf\Payment\Contract\GatewayInterface;
use Hyperf\Payment\Exception\GatewayException;
use Hyperf\Payment\Helpers\Arr;

/**
 * 手机网站支付接口2.0
 * Class WapCharge
 * @package Hyperf\Payment\Gateway\Alipay
 */
class WapCharge extends BaseAlipay implements GatewayInterface
{
    const METHOD = 'alipay.trade.wap.pay';

    /**
     * @param array $options
     * @return mixed
     */
    protected function getBizContent(array $options)
    {
        $timeoutExp = '';
        $timeExpire = intval($options['time_expire']);
        if (!empty($timeExpire)) {
            $expire                      = floor(($timeExpire - time()) / 60);
            ($expire > 0) && $timeoutExp = $expire . 'm';// 超时时间 统一使用分钟计算
        }

        $bizContent = [
            'body'            => $options['body'] ?? '',
            'subject'         => $options['subject'] ?? '',
            'out_trade_no'    => $options['trade_no'] ?? '',
            'timeout_express' => $timeoutExp,
            'time_expire'     => $timeExpire ? date('Y-m-d H:i', $timeExpire) : '',
            'total_amount'    => $options['amount'] ?? '',
            'auth_token'      => $options['auth_token'] ?? '',
            'goods_type'      => $options['goods_type'] ?? '',
            'passback_params' => $options['return_params'] ?? '',
            'quit_url'        => $options['quit_url'] ?? '',
            'product_code'    => 'QUICK_WAP_WAY',
            'promo_params'    => $options['promo_params'] ?? '',
            'extend_params'   => $options['extend_params'] ?? '',
            // 使用禁用列表
            //'enable_pay_channels' => '',
            'disable_pay_channels' => implode(self::$config->get('limit_pay', ''), ','),
            'store_id'             => $options['store_id'] ?? '',
            'specified_channel'    => $options['specified_channel'] ?? '',
            'business_params'      => $options['business_params'] ?? '',
            'ext_user_info'        => $options['ext_user_info'] ?? '',
        ];
        $bizContent = Arr::paraFilter($bizContent);

        return $bizContent;
    }

    /**
     * 获取第三方返回结果
     * @param array $options
     * @return mixed
     * @throws GatewayException
     */
    public function request(array $options)
    {
        try {
            $params = $this->buildParams(self::METHOD, $options);

            return sprintf('%s?%s', $this->gatewayUrl, http_build_query($params));
        } catch (GatewayException $e) {
            throw $e;
        }
    }
}
