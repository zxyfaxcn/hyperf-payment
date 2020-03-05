<?php
declare(strict_types = 1);
namespace Hyperf\Payment\Exception;

class ClassNotFoundException extends \RuntimeException
{

    public function __construct($message = "", $code = 0)
    {
        parent::__construct($message, $code);
    }

}
