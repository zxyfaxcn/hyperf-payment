<?php
declare(strict_types = 1);
namespace Hyperf\Payment\Exception;

use Throwable;

class GatewayException extends \RuntimeException
{
    private $throw;

    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->throw = $previous;
    }

    public function getRaw()
    {
        return $this->throw;
    }
}
