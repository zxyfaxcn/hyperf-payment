<?php
declare(strict_types=1);
namespace Hyperf\Payment\Gateway\Alipay;


use Hyperf\Payment\Contract\GatewayInterface;
use Hyperf\Payment\Exception\GatewayException;
use Hyperf\Payment\Helpers\Arr;
use Hyperf\Payment\Payment;

/**
 * 网商银行全渠道收单业务订单创建
 * Class TradeBankCreate
 * @package Hyperf\Payment\Gateway\Alipay
 */
class TradeBankCreate extends BaseAlipay implements GatewayInterface
{
    const METHOD = 'mybank.payment.trade.order.create';

    /**
     * @param array $options
     * @return mixed
     */
    protected function getBizContent(array $options)
    {
        $bizContent = [
            'partner_id'       => $options['partner_id'] ?? '',
            'out_trade_no'     => $options['trade_no'] ?? '',
            'recon_related_no' => $options['recon_related_no'] ?? '',
            'pd_code'          => $options['pd_code'] ?? '',
            'ev_code'          => $options['ev_code'] ?? '',
            'total_amount'     => $options['total_amount'] ?? '',
            'currency_code'    => $options['currency_code'] ?? '',
            'goods_info'       => $options['goods_info'] ?? '',
            'seller_id'        => $options['seller_id'] ?? '',
            'pay_type'         => $options['pay_type'] ?? '',
            'pay_date'         => $options['pay_date'] ?? '',
            'remark'           => $options['remark'] ?? '',
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
            $ret    = $this->get($this->gatewayUrl, $params);
            $retArr = json_decode($ret, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new GatewayException(sprintf('format trade bank create get error, [%s]', json_last_error_msg()), Payment::FORMAT_DATA_ERR, ['raw' => $ret]);
            }

            $content = $retArr['mybank_payment_trade_order_create_response'];
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
