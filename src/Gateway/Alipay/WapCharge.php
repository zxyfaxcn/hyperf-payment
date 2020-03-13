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

namespace Hyperf\Payment\Gateway\Alipay;

use Hyperf\Payment\Contract\GatewayInterface;
use Hyperf\Payment\Exception\GatewayException;
use Hyperf\Payment\Helpers\Arr;

/**
 * 手机网站支付接口2.0
 * Class WapCharge.
 */
class WapCharge extends BaseAlipay implements GatewayInterface
{
    const METHOD = 'alipay.trade.wap.pay';

    /**
     * 获取第三方返回结果.
     *
     * @param array $options
     *
     * @return mixed
     * @throws \Hyperf\Payment\Exception\GatewayException
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

    /**
     * @return mixed
     */
    protected function getBizContent(array $options)
    {
        $timeoutExp = '';
        $timeExpire = intval($options['time_expire']);
        if (! empty($timeExpire)) {
            $expire = floor(($timeExpire - time()) / 60);
            ($expire > 0) && $timeoutExp = $expire . 'm'; // 超时时间 统一使用分钟计算
        }

        $bizContent = [
            'body' => $options['body'] ?? '',
            'subject' => $options['subject'] ?? '',
            'out_trade_no' => $options['trade_no'] ?? '',
            'timeout_express' => $timeoutExp,
            'time_expire' => $timeExpire ? date('Y-m-d H:i', $timeExpire) : '',
            'total_amount' => $options['amount'] ?? '',
            'auth_token' => $options['auth_token'] ?? '',
            'goods_type' => $options['goods_type'] ?? '',
            'passback_params' => $options['return_params'] ?? '',
            'quit_url' => $options['quit_url'] ?? '',
            'product_code' => 'QUICK_WAP_WAY',
            'promo_params' => $options['promo_params'] ?? '',
            'extend_params' => $options['extend_params'] ?? '',
            // 使用禁用列表
            //'enable_pay_channels' => '',
            'disable_pay_channels' => implode($this->getConfig('limit_pay', ''), ','),
            'store_id' => $options['store_id'] ?? '',
            'specified_channel' => $options['specified_channel'] ?? '',
            'business_params' => $options['business_params'] ?? '',
            'ext_user_info' => $options['ext_user_info'] ?? '',
        ];
        return Arr::paraFilter($bizContent);
    }
}
