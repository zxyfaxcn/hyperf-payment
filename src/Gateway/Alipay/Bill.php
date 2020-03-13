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
use Hyperf\Payment\Payment;

/**
 * 为方便商户快速查账，支持商户通过本接口获取商户离线账单下载地址
 * Class Bill.
 */
class Bill extends BaseAlipay implements GatewayInterface
{
    const METHOD = 'alipay.data.dataservice.bill.downloadurl.query';

    public function request(array $options)
    {
        try {
            $params = $this->buildParams(self::METHOD, $options);
            $ret = $this->get($this->gatewayUrl, $params);
            $retArr = json_decode($ret, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new GatewayException(sprintf('format bill data get error, [%s]', json_last_error_msg()), Payment::FORMAT_DATA_ERR, ['raw' => $ret]);
            }

            $content = $retArr['alipay_data_dataservice_bill_downloadurl_query_response'];
            if ($content['code'] !== self::REQ_SUC) {
                throw new GatewayException(sprintf('request get failed, msg[%s], sub_msg[%s]', $content['msg'], $content['sub_msg']), Payment::SIGN_ERR, $content);
            }

            $signFlag = $this->verifySign($content, $retArr['sign']);
            if (! $signFlag) {
                throw new GatewayException('check sign failed', Payment::SIGN_ERR, $retArr);
            }

            return $content;
        } catch (GatewayException $e) {
            throw $e;
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function getBizContent(array $requestParams)
    {
        $bizContent = [
            'bill_type' => $requestParams['bill_type'] ?? 'trade',
            'bill_date' => $requestParams['bill_date'] ?? '', // 日账单格式为yyyy-MM-dd
        ];
        return Arr::paraFilter($bizContent);
    }
}
