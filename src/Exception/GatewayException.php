<?php

declare(strict_types = 1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://doc.hyperf.io
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf-cloud/hyperf/blob/master/LICENSE
 */

namespace Hyperf\Payment\Exception;

use Throwable;

class GatewayException extends \Exception
{
    private $throw;

    public function __construct($message = '', $code = 0, $raw = [])
    {
        parent::__construct($message, $code);
        $this->throw = $raw;
    }

    public function getRaw()
    {
        return $this->throw;
    }
}
