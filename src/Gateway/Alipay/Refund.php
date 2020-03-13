<?php
declare(strict_types=1);

namespace Hyperf\Payment\Gateway\Alipay;

use Hyperf\Payment\Contract\GatewayInterface;
use Hyperf\Payment\Exception\GatewayException;
use Hyperf\Payment\Gateway\BaseAlipay;
use Hyperf\Payment\Helpers\Arr;
use Hyperf\Payment\Payment;

/**
 * 统一收单交易退款接口
 **/
class Refund extends BaseAlipay implements GatewayInterface
{
    const METHOD = 'alipay.trade.refund';

    /**
     * @param array $options
     * @return mixed
     */
    protected function getBizContent(array $options)
    {
        $bizContent = [
            'out_trade_no'              => $options['trade_no'] ?? '',
            'trade_no'                  => $options['transaction_id'] ?? '',
            'refund_amount'             => $options['refund_fee'] ?? '',
            'refund_currency'           => $options['refund_currency'] ?? 'CNY',
            'refund_reason'             => $options['reason'] ?? '',
            'out_request_no'            => $options['refund_no'] ?? '',
            'operator_id'               => $options['operator_id'] ?? '',
            'store_id'                  => $options['store_id'] ?? '',
            'terminal_id'               => $options['terminal_id'] ?? '',
            'goods_detail'              => $options['goods_detail'] ?? '',
            'refund_royalty_parameters' => $options['refund_royalty_parameters'] ?? '',
            'org_pid'                   => $options['org_pid'] ?? '',
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
     */
    public function request(array $options)
    {
        try {
            $params = $this->buildParams(self::METHOD, $options);
            $ret    = $this->get($this->gatewayUrl, $params);
            $retArr = json_decode($ret, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new GatewayException(sprintf('format refund data get error, [%s]', json_last_error_msg()), Payment::FORMAT_DATA_ERR, ['raw' => $ret]);
            }

            $content = $retArr['alipay_trade_refund_response'];
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
