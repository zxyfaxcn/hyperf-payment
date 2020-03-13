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

namespace Hyperf\Payment\Event;

class PaymentMeta
{
    /**
     * @var mixed
     */
    public $request;

    /**
     * @var object
     */
    public $factory;

    /**
     * @var mixed
     */
    public $response;

    public function __construct(object $factory, $requestParams, $response)
    {
        $this->factory = $factory;
        $this->request = $requestParams;
        $this->response = $response;
    }
}
