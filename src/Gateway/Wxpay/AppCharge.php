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

use Hyperf\Payment\Contract\GatewayInterface;
use Hyperf\Payment\Exception\GatewayException;

/**
 * Class AppCharge.
 * @desc    app支付
 */
class AppCharge extends BaseWxPay implements GatewayInterface
{
    const METHOD = 'pay/unifiedorder';

    /**
     * @param array $options
     *
     * @return array|false|mixed|string
     * @throws \Hyperf\Payment\Exception\GatewayException
     */
    public function request(array $options)
    {
        try {
            return $this->requestWXApi(self::METHOD, $options);
        } catch (GatewayException $e) {
            throw $e;
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function getSelfParams(array $requestParams)
    {
        $limitPay = $this->getConfig('limit_pay', '');
        if ($limitPay) {
            $limitPay = $limitPay[0];
        } else {
            $limitPay = '';
        }
        $nowTime = time();
        $timeExpire = intval($requestParams['time_expire']);
        if (! empty($timeExpire)) {
            $timeExpire = date('YmdHis', $timeExpire);
        } else {
            $timeExpire = date('YmdHis', $nowTime + 1800); // 默认半小时过期
        }

        $receipt = $requestParams['receipt'] ?? false;
        $totalFee = bcmul($requestParams['amount'], 100, 0);
        $sceneInfo = $requestParams['scene_info'] ?? '';
        if ($sceneInfo) {
            $sceneInfo = json_encode(['store_info' => $sceneInfo]);
        } else {
            $sceneInfo = '';
        }

        /* @var TYPE_NAME $selfParams */
        return [
            'device_info' => $requestParams['device_info'] ?? '',
            'body' => $requestParams['subject'] ?? '',
            'detail' => $requestParams['body'] ?? '',
            'attach' => $requestParams['return_param'] ?? '',
            'out_trade_no' => $requestParams['trade_no'] ?? '',
            'fee_type' => $this->getConfig('fee_type', 'CNY'),
            'total_fee' => $totalFee,
            'spbill_create_ip' => $requestParams['client_ip'] ?? '',
            'time_start' => date('YmdHis', $nowTime),
            'time_expire' => $timeExpire,
            'goods_tag' => $requestParams['goods_tag'] ?? '',
            'notify_url' => $this->getConfig('notify_url', ''),
            'trade_type' => 'APP',
            'limit_pay' => $limitPay,
            'receipt' => $receipt === true ? 'Y' : '',
            'scene_info' => $requestParams['scene_info'] ?? '', // 场景信息
        ];
    }
}
