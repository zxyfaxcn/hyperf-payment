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
 * PC场景下单并支付 / 统一收单下单并支付页面接口
 * Class WebCharge.
 */
class WebCharge extends BaseAlipay implements GatewayInterface
{
    const METHOD = 'alipay.trade.page.pay';

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
     * 构建请求参数.
     *
     * @param array $options
     *
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
            'out_trade_no' => $options['trade_no'] ?? '',
            'product_code' => 'FAST_INSTANT_TRADE_PAY',
            'total_amount' => $options['amount'] ?? '',
            'subject' => $options['subject'] ?? '',
            'body' => $options['body'] ?? '',
            'time_expire' => $timeExpire ? date('Y-m-d H:i:s', $timeExpire) : '',
            'goods_detail' => $options['goods_detail'] ?? '',
            'passback_params' => $options['return_params'] ?? '',
            'extend_params' => $options['extend_params'] ?? '',
            'goods_type' => $options['goods_type'] ?? '',
            'timeout_express' => $timeoutExp,
            'promo_params' => $options['promo_params'] ?? '',
            'royalty_info' => $options['royalty_info'] ?? '',
            'sub_merchant' => $options['sub_merchant'] ?? '',
            // 使用禁用列表
            //'enable_pay_channels' => '',
            'store_id' => $options['store_id'] ?? '',
            'disable_pay_channels' => implode($this->getConfig('limit_pay', ''), ','),
            'qr_pay_mode' => $options['qr_pay_mode'] ?? '2',
            'qrcode_width' => $options['qrcode_width'] ?? '',
            'settle_info' => $options['settle_info'] ?? '',
            'invoice_info' => $options['invoice_info'] ?? '',
            'agreement_sign_params' => $options['agreement_sign_params'] ?? '',
            'integration_type' => $options['integration_type'] ?? 'PCWEB',
            'request_from_url' => $options['request_from_url'] ?? '',
            'business_params' => $options['business_params'] ?? '',
            'ext_user_info' => $options['ext_user_info'] ?? '',
        ];
        return Arr::paraFilter($bizContent);
    }
}
