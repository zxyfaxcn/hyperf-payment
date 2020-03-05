<?php
declare(strict_types = 1);
namespace Hyperf\Payment\Contract;

/**
 * Interface QueryInterface
 * @package Hyperf\Payment\Contract
 * @desc 结算查询
 */
interface QueryInterface
{
    /**
     * 交易查询
     * @param array $options
     * @return mixed
     */
    public function tradeQuery(array $options);

    /**
     * 退款查询
     * @param array $options
     * @return mixed
     */
    public function refundQuery(array $options);

    /**
     * 转账查询
     * @param array $options
     * @return mixed
     */
    public function transferQuery(array $options);

    /**
     * 账单查询
     * @param array $options
     * @return mixed
     */
    public function billDownload(array $options);

    /**
     * 打款结算查询
     * @param array $options
     * @return mixed
     */
    public function settleDownload(array $options);
}
