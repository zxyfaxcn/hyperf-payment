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

/**
 * Interface PayNotifyInterface.
 * @desc 异步通知的接口处理
 */
interface PayNotifyInterface
{
    /**
     * @param string $channel 通知的渠道，如：支付宝、微信
     * @param string $notifyType 通知的类型，如：支付、退款
     * @param string $notifyWay 通知的方式，如：异步 async，同步 sync
     * @param array $notifyData 通知的数据
     *
     * @return mixed
     */
    public function handle(string $channel, string $notifyType, string $notifyWay, array $notifyData);
}
