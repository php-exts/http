<?php
declare (strict_types = 1);

namespace Zeus\Http;

use Zeus\Http\StatusCode;
use Psr\Http\Message\ResponseInterface;

/**
 * Http Client Adapter
 *
 * @author imxieke <oss@live.hk>
 * @copyright (c) 2025 CloudFlying
 * @date 2025/10/17 18:09:29
 */
class Adapter
{

    use StatusCode;

    /**
     * Http Method
     *
     * @var string
     */
    protected string $method;

    /**
     * HTTP request methods
     *
     * 501 Not Implemented LINK PURGE
     *
     * @see @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Methods
     * @var array
     */
    protected array $methods = [
        'CONNECT',
        'DELETE',
        'GET',
        'HEAD',
        'OPTIONS',
        'PATCH',
        'POST',
        'PURGE',
        'PUT',
        'TRACE'
    ];

    public const string METHOD_CONNECT = 'CONNECT';
    public const string METHOD_DELETE  = 'DELETE';
    public const string METHOD_GET     = 'GET';
    public const string METHOD_HEAD    = 'HEAD';
    public const string METHOD_OPTIONS = 'OPTIONS';
    public const string METHOD_PATCH   = 'PATCH';
    public const string METHOD_POST    = 'POST';
    public const string METHOD_PURGE   = 'PURGE';
    public const string METHOD_PUT     = 'PUT';
    public const string METHOD_TRACE   = 'TRACE';

    /**
     * Request Url
     *
     * @var string
     */
    protected string $url;

    /**
     * Request Params
     *
     * @var string
     */
    protected string $uri;

    /**
     * Http Request Headers
     *
     * @var array
     * @author CloudFlying
     * @date 2025/10/17 09:56:11
     */
    protected array $headers = [
        'User-Agent' => "Zeus/" . PHP_VERSION
    ];

    /**
     * Http Cookies
     *
     * @var array
     * @author CloudFlying
     * @date 2025/10/17 09:56:43
     */
    protected array $cookies = [];

    /**
     * Http Request Timeout
     *
     * @var int
     * @author CloudFlying
     * @date 2025/10/17 18:15:21
     */
    protected $timeout = 10;

    public bool $thorwError = false;

    /**
     * Response Object
     *
     * @var ResponseInterface
     * @author CloudFlying
     * @date 2025/10/17 19:22:39
     */
    protected ResponseInterface $response;

    /**
     * Http Client Handle
     *
     * @var object
     */
    protected $client;

    /**
     * 获取请求超时时间
     *
     * @return int
     * @author imxieke <oss@live.hk>
     * @date 2025/10/17 18:16:48
     */
    public function getTimeout()
    {
        return $this->timeout;
    }

    /**
     * 设置请求超时时间
     *
     * @param int|float $timeout
     * @return static
     * @author imxieke <oss@live.hk>
     * @date 2025/10/17 18:17:13
     */
    public function setTimeout(int|float $timeout)
    {
        $this->timeout = $timeout;
        return $this;
    }

    public function error(bool $error = true)
    {
        $this->thorwError = $error;
        return $this;
    }
}
