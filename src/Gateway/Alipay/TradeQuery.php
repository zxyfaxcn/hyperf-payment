<?php
declare(strict_types=1);

namespace Hyperf\Payment\Gateway\Alipay;


use Hyperf\Payment\Contract\GatewayInterface;
use Hyperf\Payment\Exception\GatewayException;
use Hyperf\Payment\Helpers\Arr;
use Hyperf\Payment\Payment;

class TradeQuery extends BaseAlipay implements GatewayInterface
{
    const METHOD = 'alipay.trade.query';

    /**
     * @param array $options
     * @return mixed
     */
    protected function getBizContent(array $options)
    {
        $bizContent = [
            'out_trade_no'  => $options['trade_no'] ?? '',
            'trade_no'      => $options['transaction_id'] ?? '',
            'query_options' => $options['query_options'] ?? '',
            'org_pid'       => $options['org_pid'] ?? '',
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
                throw new GatewayException(sprintf('format refund data get error, [%s]', json_last_error_msg()), Payment::FORMAT_DATA_ERR, ['raw' => $ret]);
            }

            $content = $retArr['alipay_trade_query_response'];
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