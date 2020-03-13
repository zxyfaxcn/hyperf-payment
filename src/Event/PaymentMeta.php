<?php
declare(strict_types = 1);
namespace Hyperf\Payment\Event;

class PaymentMeta
{
    /**
     * @var mixed $data
     */
    public $request;

    /**
     * @var object $factory
     */
    public $factory;

    /**
     * @var mixed $response
     */
    public $response;

    public function __construct(object $factory, $requestParams, $response)
    {
        $this->factory  = $factory;
        $this->request     = $requestParams;
        $this->response = $response;
    }
}
