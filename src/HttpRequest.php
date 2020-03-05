<?php
declare(strict_types = 1);
namespace Hyperf\Payment;

use GuzzleHttp\Client;
use Hyperf\Guzzle\HandlerStackFactory;
use Psr\Http\Message\ResponseInterface;

/**
 * Trait HttpRequest
 * @package Hyperf\Payment
 */
trait HttpRequest
{
    /**
     * 设置请求选项
     * @var array
     */
    private $options = [
        'max_connections' => 50
    ];

    /**
     * @var \Hyperf\Guzzle\ClientFactory
     */
    private $clientFactory;

    public function __construct(array $options = [])
    {
        $this->options = (is_array($options) && count($options) > 0) ? $options : $this->options;
    }

    /**
     * @param array $options
     */
    public function setOptions(array $options) : void
    {
        $this->options = $options;
    }

    /**
     * @return array
     */
    public function getOptions() : array
    {
        return $this->options;
    }

    /**
     * @param string $url
     * @param array  $query
     * @param array  $headers
     *
     * @return mixed|string
     */
    protected function get(string $url, array $query = [], array $headers = [])
    {
        return $this->request('get', $url, [
            'headers'     => $headers,
            'query'       => $query,
            'http_errors' => false,
        ]);
    }

    /**
     * @param string $url
     * @param array  $params
     * @param array  $headers
     *
     * @return mixed|string
     */
    protected function post(string $url, array $params = [], array $headers = [])
    {
        return $this->request('post', $url, [
            'headers'     => $headers,
            'form_params' => $params,
            'http_errors' => false,
        ]);
    }

    /**
     * @param string $url
     * @param array  $params
     * @param array  $headers
     *
     * @return mixed|string
     */
    protected function postJson(string $url, array $params = [], array $headers = [])
    {
        return $this->request('post', $url, [
            'headers'     => $headers,
            'json'        => $params,
            'http_errors' => false,
        ]);
    }

    /**
     * @param string $url
     * @param string $xmlData
     * @param array  $headers
     *
     * @return mixed|string
     */
    protected function postXML(string $url, string $xmlData, array $headers = [])
    {
        return $this->request('post', $url, [
            'headers'     => $headers,
            'body'        => $xmlData,
            'http_errors' => false,
        ]);
    }

    /**
     * 发送表单数据
     *
     * @param string $url
     * @param array  $formData
     * @param array  $headers
     *
     * @return array|mixed|ResponseInterface|string
     */
    protected function postForm(string $url, array $formData, array $headers = [])
    {
        return $this->request('post', $url, [
            'headers'     => $headers,
            'multipart'   => $formData,
            'http_errors' => false,
        ]);
    }

    /**
     * @return Client
     */
    private function create() : Client
    {
        $factory = new HandlerStackFactory();
        $stack   = $factory->create($this->options);
        return make(Client::class, [
            'config' => [
                'handler' => $stack
            ]
        ]);
    }

    /**
     * 发送请求
     *
     * @param string $method
     * @param string $url
     * @param array  $options
     *
     * @return mixed|string
     */
    private function request(string $method, string $url, array $options = [])
    {
        return $this->unwrapResponse($this->create()->{$method}($url, $options));
    }

    /**
     * 解析返回数据内容
     *
     * @param \Psr\Http\Message\ResponseInterface $response
     *
     * @return mixed|string
     */
    private function unwrapResponse(ResponseInterface $response)
    {
        $contentType = $response->getHeaderLine('Content-Type');
        $contents    = $response->getBody()->getContents();

        if (false !== stripos($contentType, 'json') || stripos($contentType, 'javascript')) {
            return json_decode($contents, true);
        } elseif (false !== stripos($contentType, 'xml')) {
            return json_decode(json_encode(simplexml_load_string($contents)), true);
        }

        return $contents;
    }

}
