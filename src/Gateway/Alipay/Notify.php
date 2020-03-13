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

use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\Payment\Exception\GatewayException;
use Hyperf\Payment\Payment;
use Hyperf\Utils\Context;

/**
 * Class Notify.
 * @desc    处理回调通知
 */
class Notify extends BaseAlipay
{
    /**
     * 获取请求数据.
     * @return array|mixed|string
     * @throws \Hyperf\Payment\Exception\GatewayException
     */
    public function request()
    {
        $resArr = $this->getNotifyData();
        if (empty($resArr)) {
            throw new GatewayException('the notify data is empty', Payment::NOTIFY_DATA_EMPTY);
        }

        if (isset($resArr['notify_type'], $resArr['trade_status'])) {
            $notifyWay = 'async'; // 异步
        } else {
            $notifyWay = 'sync'; // 同步
        }

        $sign = $resArr['sign'];
        $signType = $resArr['sign_type'];
        unset($resArr['sign'], $resArr['sign_type']);

        if ($this->verifySignForASync($resArr, $sign, $signType) === false) {
            throw new GatewayException('check notify data sign failed', Payment::SIGN_ERR, $resArr);
        }

        if (! isset($resArr['app_id']) || $resArr['app_id'] != $this->getConfig('app_id', '')) {
            throw new GatewayException('mch info is error', Payment::MCH_INFO_ERR, $resArr);
        }

        return [
            'notify_type' => 'pay',
            'notify_way' => $notifyWay,
            'notify_data' => $resArr,
        ];
    }

    /**
     * 响应数据.
     *
     * @param bool $flag
     *
     * @return string
     */
    public function response(bool $flag): string
    {
        if ($flag) {
            return 'success';
        }
        return 'fail';
    }

    /**
     * notify 不需要实现该方法.
     *
     * @param array $options
     *
     * @return mixed
     */
    protected function getBizContent(array $options)
    {
        return [];
    }

    protected function getNotifyData()
    {
        $request = Context::get(RequestInterface::class);
        $data = $request->all();
        if (empty($data) || ! is_array($data)) {
            return [];
        }

        return $data;
    }
}
