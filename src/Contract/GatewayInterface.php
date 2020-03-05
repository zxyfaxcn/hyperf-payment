<?php
declare(strict_types = 1);
namespace Hyperf\Payment\Contract;

interface GatewayInterface
{
    /**
     * 获取第三方返回结果
     * @param array $options
     *
     * @return mixed
     */
    public function request(array $options);
}
