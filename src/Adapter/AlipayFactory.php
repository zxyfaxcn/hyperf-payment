<?php
declare(strict_types = 1);
namespace Hyperf\Payment\Adapter;

use Hyperf\Payment\Contract\GatewayInterface;
use Hyperf\Payment\Contract\PayInterface;
use Hyperf\Payment\Contract\PayNotifyInterface;
use Hyperf\Payment\Contract\QueryInterface;
use Hyperf\Payment\Exception\GatewayException;
use Hyperf\Payment\Payment;

class AlipayFactory implements GatewayInterface, PayInterface, PayNotifyInterface, QueryInterface
{

    /**
     * @inheritDoc
     */
    public function request(array $options)
    {

    }

    /**
     * @inheritDoc
     */
    public function pay(string $channel, array $options)
    {
        $class = $this->getChargeClass($channel);
        if (!class_exists($class)) {
            throw new InvalidArgumentException(sprintf('Gateway [%s] not exists.', $class), Payment::CLASS_NOT_EXIST);
        }

        try {
            /**
             * @var GatewayInterface $charge
             */
            $charge = make($class);
            return $charge->request($options);
        } catch (GatewayException $exception) {
            throw $exception;
        }
    }

    /**
     * @inheritDoc
     */
    public function refund(array $options)
    {

    }

    /**
     * @inheritDoc
     */
    public function notify(PayNotifyInterface $callback)
    {

    }

    /**
     * @inheritDoc
     */
    public function cancel(array $options)
    {

    }

    /**
     * @inheritDoc
     */
    public function close(array $options)
    {

    }

    /**
     * @inheritDoc
     */
    public function handle(string $channel, string $notifyType, string $notifyWay, array $notifyData)
    {

    }

    /**
     * @inheritDoc
     */
    public function tradeQuery(array $options)
    {

    }

    /**
     * @inheritDoc
     */
    public function refundQuery(array $options)
    {

    }

    /**
     * @inheritDoc
     */
    public function transferQuery(array $options)
    {

    }

    /**
     * @inheritDoc
     */
    public function billDownload(array $options)
    {

    }

    /**
     * @inheritDoc
     */
    public function settleDownload(array $options)
    {

    }

    /**
     * 获取支付类
     *
     * @param string $channel
     *
     * @return string
     */
    private function getChargeClass(string $channel)
    {
        $name = ucfirst(str_replace(['-', '_', ''], '', $channel));
        return "Hyperf\\Payment\\Gateways\\Alipay\\{$name}Charge";
    }
}
