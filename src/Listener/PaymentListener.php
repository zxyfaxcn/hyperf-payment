<?php
declare(strict_types = 1);
namespace Hyperf\Payment\Listener;

use Hyperf\Contract\ConfigInterface;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Event\Contract\ListenerInterface;
use Hyperf\Logger\LoggerFactory;
use Hyperf\Payment\Adapter\AlipayFactory;
use Hyperf\Payment\Adapter\WxpayFactory;
use Hyperf\Payment\ClientFactory;
use Hyperf\Payment\Event\PaymentMeta;
use Hyperf\Payment\Payment;
use Hyperf\Utils\Codec\Json;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

class PaymentListener implements ListenerInterface
{
    /**
     * @var string
     */
    private $proxy;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * @var StdoutLoggerInterface
     */
    private $logger;

    public function __construct(ContainerInterface $container, ConfigInterface $config)
    {
        $this->container = $container;
        $this->config    = $config;
        $this->logger    = $this->container->get(LoggerFactory::class)->get('payment');
    }

    public function listen() : array
    {
        return [
            PaymentMeta::class
        ];
    }

    public function process(object $event)
    {
        /**
         * @var PaymentMeta $event
         */
        $factory = $event->factory;
        if ($factory instanceof AlipayFactory) {
            $this->proxy = ClientFactory::ALIPAY;
        } elseif ($factory instanceof WxpayFactory){
            $this->proxy = ClientFactory::WECHAT;
        }
        $this->logger->info(sprintf('调取支付渠道[%s],请求参数[%s],数据响应[%s]', $this->proxy, Json::encode($event->request), Json::encode($event->response)));
    }
}
