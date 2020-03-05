<?php
declare(strict_types =1);
namespace Hyperf\Payment\Adapter;
use Hyperf\Payment\Contract\GatewayInterface;
use Hyperf\Payment\Contract\PayInterface;
use Hyperf\Payment\Contract\PayNotifyInterface;
use Hyperf\Payment\Contract\QueryInterface;

class WxpayFactory implements GatewayInterface,PayInterface,PayNotifyInterface,QueryInterface{

    /**
     * @inheritDoc
     */
    public function request(array $options)
    {
        // TODO: Implement request() method.
    }

    /**
     * @inheritDoc
     */
    public function pay(string $channel, array $options)
    {
        // TODO: Implement pay() method.
    }

    /**
     * @inheritDoc
     */
    public function refund(array $options)
    {
        // TODO: Implement refund() method.
    }

    /**
     * @inheritDoc
     */
    public function notify(PayNotifyInterface $callback)
    {
        // TODO: Implement notify() method.
    }

    /**
     * @inheritDoc
     */
    public function cancel(array $options)
    {
        // TODO: Implement cancel() method.
    }

    /**
     * @inheritDoc
     */
    public function close(array $options)
    {
        // TODO: Implement close() method.
    }

    /**
     * @inheritDoc
     */
    public function handle(string $channel, string $notifyType, string $notifyWay, array $notifyData)
    {
        // TODO: Implement handle() method.
    }

    /**
     * @inheritDoc
     */
    public function tradeQuery(array $options)
    {
        // TODO: Implement tradeQuery() method.
    }

    /**
     * @inheritDoc
     */
    public function refundQuery(array $options)
    {
        // TODO: Implement refundQuery() method.
    }

    /**
     * @inheritDoc
     */
    public function transferQuery(array $options)
    {
        // TODO: Implement transferQuery() method.
    }

    /**
     * @inheritDoc
     */
    public function billDownload(array $options)
    {
        // TODO: Implement billDownload() method.
    }

    /**
     * @inheritDoc
     */
    public function settleDownload(array $options)
    {
        // TODO: Implement settleDownload() method.
    }
}
