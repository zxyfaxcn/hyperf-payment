<?php
declare(strict_types = 1);

namespace Hyperf\Payment\Gateway\Alipay;

use Hyperf\Payment\Contract\GatewayInterface;
use Hyperf\Payment\Contract\QueryInterface;
use Hyperf\Payment\Exception\GatewayException;
use Hyperf\Payment\Helpers\Arr;
use Hyperf\Payment\Payment;

/**
 * 商户可通过该接口查询转账订单的状态、支付时间等相关信息，主要应用于B2C转账订单查询的场景
 * Class TransferQuery
 * @package Hyperf\Payment\Gateway\Alipay
 */
class TransferQuery extends BaseAlipay implements GatewayInterface
{
    const METHOD = 'alipay.fund.trans.order.query';

    /**
     * @param array $options
     *
     * @return mixed
     */
    protected function getBizContent(array $options)
    {
        $bizContent = [
            'out_biz_no' => $options['trans_no'] ?? '',
            'order_id'   => $options['transaction_id'] ?? '',
        ];
        $bizContent = Arr::paraFilter($bizContent);

        return $bizContent;
    }

    /**
     * 获取第三方返回结果
     *
     * @param array $options
     *
     * @return mixed
     * @throws GatewayException
     */
    public function request(array $options)
    {
        try {
            $params = $this->buildParams(self::METHOD, $options);
            $ret    = $this->get($this->gatewayUrl, $params);
            $retArr = json_decode($ret, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new GatewayException(sprintf('format transfer query data get error, [%s]', json_last_error_msg()), Payment::FORMAT_DATA_ERR, ['raw' => $ret]);
            }

            $content = $retArr['alipay_fund_trans_order_query_response'];
            if ($content['code'] !== self::REQ_SUC) {
                throw new GatewayException(sprintf('request get failed, msg[%s], sub_msg[%s]', $content['msg'], $content['sub_msg']), Payment::SIGN_ERR, $content);
            }

            $signFlag = $this->verifySign($content, $retArr['sign']);
            if (!$signFlag) {
                throw new GatewayException('check sign failed', Payment::SIGN_ERR, $retArr);
            }

            return $content;
        } catch (GatewayException $e) {
            throw $e;
        }
    }
}
