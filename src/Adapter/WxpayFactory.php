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

namespace Hyperf\Payment\Adapter;

use Hyperf\Payment\Contract\GatewayInterface;
use Hyperf\Payment\Contract\PayInterface;
use Hyperf\Payment\Contract\PayNotifyInterface;
use Hyperf\Payment\Contract\QueryInterface;

class WxpayFactory implements GatewayInterface, PayInterface, PayNotifyInterface, QueryInterface
{
    /**
     * {@inheritdoc}
     */
    public function request(array $options)
    {
        // TODO: Implement request() method.
    }

    /**
     * {@inheritdoc}
     */
    public function pay(string $channel, array $options)
    {
        // TODO: Implement pay() method.
    }

    /**
     * {@inheritdoc}
     */
    public function refund(array $options)
    {
        // TODO: Implement refund() method.
    }

    /**
     * {@inheritdoc}
     */
    public function notify(PayNotifyInterface $callback)
    {
        // TODO: Implement notify() method.
    }

    /**
     * {@inheritdoc}
     */
    public function cancel(array $options)
    {
        // TODO: Implement cancel() method.
    }

    /**
     * {@inheritdoc}
     */
    public function close(array $options)
    {
        // TODO: Implement close() method.
    }

    /**
     * {@inheritdoc}
     */
    public function handle(string $channel, string $notifyType, string $notifyWay, array $notifyData)
    {
        // TODO: Implement handle() method.
    }

    /**
     * {@inheritdoc}
     */
    public function tradeQuery(array $options)
    {
        // TODO: Implement tradeQuery() method.
    }

    /**
     * {@inheritdoc}
     */
    public function refundQuery(array $options)
    {
        // TODO: Implement refundQuery() method.
    }

    /**
     * {@inheritdoc}
     */
    public function transferQuery(array $options)
    {
        // TODO: Implement transferQuery() method.
    }

    /**
     * {@inheritdoc}
     */
    public function billDownload(array $options)
    {
        // TODO: Implement billDownload() method.
    }

    /**
     * {@inheritdoc}
     */
    public function settleDownload(array $options)
    {
        // TODO: Implement settleDownload() method.
    }
}
