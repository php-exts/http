<?php
declare(strict_types=1);

namespace Zeus\Http\Adapter;

use Zeus\Http\Adapter;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Stream\Stream;
use GuzzleHttp\RequestOptions;
use GuzzleHttp\Message\Response;
use Psr\Http\Message\UriInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Event\CompleteEvent;
use GuzzleHttp\Event\MessageCompleteEvent;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Exception\GuzzleException;

/**
 * Guzzle Http Adapter
 *
 * @method ResponseInterface get(string|UriInterface $uri, array $options = []) Create and send an HTTP GET request.
 * @method ResponseInterface head(string|UriInterface $uri, array $options = []) Create and send an HTTP GET request.
 * @method ResponseInterface put(string|UriInterface $uri, array $options = []) Create and send an HTTP GET request.
 * @method ResponseInterface delete(string|UriInterface $uri, array $options = []) Create and send an HTTP GET request.
 * @method ResponseInterface patch(string|UriInterface $uri, array $options = []) Create and send an HTTP GET request.
 * @method ResponseInterface connect(string|UriInterface $uri, array $options = []) Create and send an HTTP GET request.
 * @method ResponseInterface options(string|UriInterface $uri, array $options = []) Create and send an HTTP GET request.
 * @method ResponseInterface post(string|UriInterface $uri, array $options = []) Create and send an HTTP POST request.
 *
 * @author imxieke <oss@live.hk>
 * @copyright (c) 2025 CloudFlying
 * @date 2025/10/17 19:02:13
 */
class Guzzle extends Adapter
{
    /**
     * Http Client Handler
     *
     * @var Client
     * @author CloudFlying
     * @date 2025/10/17 18:27:57
     */
    protected Client $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    /**
     * Add Middleware
     *
     * @param callable $middleware
     * @return static
     * @author imxieke <oss@live.hk>
     * @date 2025/10/18 00:29:46
     */
    public function withMiddleware(callable $middleware)
    {
        $handler = $this->client->getConfig('handler');
        if ($handler instanceof HandlerStack) {
            $handler->push($middleware);
        }
        return $this;
    }

    /**
     * Create and send an HTTP request.
     *
     * @param string $method HTTP method.
     * @param string|UriInterface $uri URI object or string.
     * @param array $options Request options
     * @return ResponseInterface
     * @author imxieke <oss@live.hk>
     * @date 2025/10/17 18:37:18
     */
    public function request(string $method, string|UriInterface $uri, array $options = []): self
    {
        $this->response = $this->client->request($method, $uri, $options);
        return $this;
    }

    /**
     * 获取请求状态码
     *
     * @return int
     * @author imxieke <oss@live.hk>
     * @date 2025/10/17 19:31:33
     */
    public function getStatusCode()
    {
        return $this->response->getStatusCode();
    }

    /**
     * 获取请求结果对象
     *
     * @return StreamInterface
     * @author imxieke <oss@live.hk>
     * @date 2025/10/17 19:29:53
     */
    public function getBody(): StreamInterface
    {
        return $this->response->getBody();
    }

    /**
     * 获取请求结果
     *
     * @return string
     * @author imxieke <oss@live.hk>
     * @date 2025/10/17 19:32:36
     */
    public function getContents()
    {
        return $this->response->getBody()->getContents();
    }

    /**
     * 结果转字符串
     *
     * @return string
     * @author imxieke <oss@live.hk>
     * @date 2025/10/17 19:27:44
     */
    public function __toString()
    {
        return $this->response->getBody()->getContents();
    }

    /**
     *
     * @param mixed $name
     * @param mixed $args
     * @return ResponseInterface
     * @throws \BadMethodCallException
     * @author imxieke <oss@live.hk>
     * @date 2025/10/17 19:20:15
     */
    public function __call($name, $args)
    {
        $method = strtoupper(trim($name));
        if(!in_array($method, $this->methods)) {
            throw new \BadMethodCallException("Undefined method: $name");
        }
        return $this->request($method, ...$args);
    }
}
