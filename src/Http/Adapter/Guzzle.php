<?php
declare(strict_types=1);

namespace Zeus\Http\Adapter;

use Zeus\Http\Adapter;

use GuzzleHttp\{
    Client,
    HandlerStack,
    Pool,
};
use GuzzleHttp\Stream\Stream;
use GuzzleHttp\RequestOptions;
use GuzzleHttp\Message\Response;
use GuzzleHttp\Cookie\{
    CookieJar,
    FileCookieJar,
    SessionCookieJar
};
use Psr\Http\Message\{
    MessageInterface,
    RequestInterface,
    ResponseInterface,
    ServerRequestInterface,
    StreamInterface,
    UploadedFileInterface,
    UriInterface
};

use Composer\CaBundle\CaBundle;

/**
 * Guzzle Http Adapter
 *
 * @author imxieke <oss@live.hk>
 * @copyright (c) 2025 CloudFlying
 * @date 2025/10/17 19:02:13
 */
class Guzzle
{
    /**
     * Http Client Handler
     *
     * @var Client
     * @author CloudFlying
     * @date 2025/10/17 18:27:57
     */
    protected $client;

    protected array $options = [];

    public function __construct(array $options = [])
    {
        $this->options['cert']['path'] = CaBundle::getBundledCaBundlePath();

        $this->options = array_merge($this->options, $options);
        $this->client = new Client($this->options);
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
    public function request(string $method, string|UriInterface $uri, array $options = []): ResponseInterface
    {
        $this->options = array_merge($this->options, $options);
        return $this->client->request($method, $uri, $this->options);
    }
}
