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

namespace Hyperf\Payment\Contract;

interface PayInterface
{
    /**
     * 支付操作.
     *
     * @param string $channel
     * @param array  $options
     *
     * @return mixed
     */
    public function pay(string $channel, array $options);

    /**
     * 退款操作.
     *
     * @param array $options
     *
     * @return mixed
     */
    public function refund(array $options);

    /**
     * 异步通知.
     *
     * @param \Hyperf\Payment\Contract\PayNotifyInterface $callback
     *
     * @return mixed
     */
    public function notify(PayNotifyInterface $callback);

    /**
     * 取消交易.
     *
     * @param array $options
     *
     * @return mixed
     */
    public function cancel(array $options);

    /**
     * 关闭交易.
     *
     * @param array $options
     *
     * @return mixed
     */
    public function close(array $options);
}
