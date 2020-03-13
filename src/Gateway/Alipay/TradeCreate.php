<?php
declare(strict_types=1);
namespace Hyperf\Payment\Gateway\Alipay;


use Hyperf\Payment\Contract\GatewayInterface;
use Hyperf\Payment\Exception\GatewayException;
use Hyperf\Payment\Helpers\Arr;
use Hyperf\Payment\Payment;

/**
 * 统一收单交易创建接口
 * Class TradeCreate
 * @package Hyperf\Payment\Gateway\Alipay
 */
class TradeCreate extends BaseAlipay implements GatewayInterface
{
    const METHOD = 'alipay.trade.create';

    /**
     * @param array $options
     * @return mixed
     */
    protected function getBizContent(array $options)
    {
        $bizContent = [
            'out_trade_no'          => $options['trade_no'] ?? '',
            'seller_id'             => $options['seller_id'] ?? '',
            'total_amount'          => $options['total_amount'] ?? '',
            'discountable_amount'   => $options['discountable_amount'] ?? '',
            'subject'               => $options['subject'] ?? '',
            'body'                  => $options['body'] ?? '',
            'buyer_id'              => $options['buyer_id'] ?? '',
            'goods_detail'          => $options['goods_detail'] ?? '',
            'product_code'          => $options['product_code'] ?? '',
            'operator_id'           => $options['operator_id'] ?? '',
            'store_id'              => $options['store_id'] ?? '',
            'terminal_id'           => $options['terminal_id'] ?? '',
            'extend_params'         => $options['extend_params'] ?? '',
            'timeout_express'       => $options['timeout_express'] ?? '',
            'settle_info'           => $options['settle_info'] ?? '',
            'logistics_detail'      => $options['logistics_detail'] ?? '',
            'business_params'       => $options['business_params'] ?? '',
            'receiver_address_info' => $options['receiver_address_info'] ?? '',
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
                throw new GatewayException(sprintf('format trade create get error, [%s]', json_last_error_msg()), Payment::FORMAT_DATA_ERR, ['raw' => $ret]);
            }

            $content = $retArr['alipay_trade_create_response'];
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
