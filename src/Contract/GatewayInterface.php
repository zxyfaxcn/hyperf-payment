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

interface GatewayInterface
{
    /**
     * 获取第三方返回结果.
     *
     * @param array $options
     *
     * @return mixed
     */
    public function request(array $options);
}
