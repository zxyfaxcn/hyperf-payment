<?php
declare(strict_types = 1);
namespace Hyperf\Payment\Adapter;

use Hyperf\Payment\Contract\GatewayInterface;
use Hyperf\Payment\Contract\PayInterface;
use Hyperf\Payment\Contract\PayNotifyInterface;
use Hyperf\Payment\Contract\QueryInterface;
use Hyperf\Payment\Exception\GatewayException;
use Hyperf\Payment\Gateway\Alipay\Bill;
use Hyperf\Payment\Gateway\Alipay\CancelTrade;
use Hyperf\Payment\Gateway\Alipay\CloseTrade;
use Hyperf\Payment\Gateway\Alipay\Notify;
use Hyperf\Payment\Gateway\Alipay\Refund;
use Hyperf\Payment\Gateway\Alipay\RefundQuery;
use Hyperf\Payment\Gateway\Alipay\TradeQuery;
use Hyperf\Payment\Gateway\Alipay\TransferQuery;
use Hyperf\Payment\Payment;

class AlipayFactory implements PayInterface, QueryInterface
{
    /**
     * 支付操作
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
     * 退款操作
     * @inheritDoc
     */
    public function refund(array $options)
    {
        try {
            $obj = new Refund();
            return $obj->request($options);
        } catch (GatewayException $e) {
            throw $e;
        }
    }

    /**
     * 同步异步通知
     * @inheritDoc
     */
    public function notify(PayNotifyInterface $callback)
    {
        try {
            $n    = new Notify();
            $data = $n->request(); // 获取数据
        } catch (GatewayException $e) {
            throw $e;
        }

        // 异步 async，同步 sync
        $flag = $callback->handle('Alipay', $data['notify_type'], $data['notify_way'], $data['notify_data']);

        return $n->response($flag);
    }

    /**
     * 取消交易
     * @inheritDoc
     */
    public function cancel(array $options)
    {
        try {
            $obj = new CancelTrade();
            return $obj->request($options);
        } catch (GatewayException $e) {
            throw $e;
        }
    }

    /**
     * 关闭交易
     * @inheritDoc
     */
    public function close(array $options)
    {
        try {
            $obj = new CloseTrade();
            return $obj->request($options);
        } catch (GatewayException $e) {
            throw $e;
        }
    }


    /**
     * 交易查询
     * @inheritDoc
     */
    public function tradeQuery(array $options)
    {
        try {
            $obj = new TradeQuery();
            return $obj->request($options);
        } catch (GatewayException $e) {
            throw $e;
        }
    }

    /**
     * @inheritDoc
     */
    public function refundQuery(array $options)
    {
        try {
            $obj = new RefundQuery();
            return $obj->request($options);
        } catch (GatewayException $e) {
            throw $e;
        }
    }

    /**
     * 转账查询
     * @inheritDoc
     */
    public function transferQuery(array $options)
    {
        try {
            $obj = new TransferQuery();
            return $obj->request($options);
        } catch (GatewayException $e) {
            throw $e;
        }
    }

    /**
     * 账单查询
     * @inheritDoc
     */
    public function billDownload(array $options)
    {
        try {
            $obj = new Bill();
            return $obj->request($options);
        } catch (GatewayException $e) {
            throw $e;
        }
    }

    /**
     * 打款结算查询
     * @inheritDoc
     */
    public function settleDownload(array $options)
    {
        throw new GatewayException('ali not support the method.', Payment::NOT_SUPPORT_METHOD);
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
