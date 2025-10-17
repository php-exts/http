<?php
declare (strict_types = 1);

namespace Zeus\Http;

use Zeus\Http\StatusCode;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\RequestOptions;

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
     * Base Uri
     *
     * @var string
     * @author CloudFlying
     * @date 2025/10/18 00:15:37
     */
    protected string $baseUri;

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
     * Http Options
     *
     * @var array
     * @author CloudFlying
     * @date 2025/10/18 00:06:02
     */
    protected array $options = [];

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

    public function setBaseUri(string $uri)
    {
        $this->baseUri;
        $this->options['base_uri'] = $uri;
        return $this;
    }

    /**
     * Set Header
     *
     * @param string $key Header Key
     * @param string $value Header Value
     * @return static
     * @author imxieke <oss@live.hk>
     * @date 2025/10/18 00:16:55
     */
    public function withHeader(string $key, string $value)
    {
        $this->headers[$key] = $value;
        return $this;
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
        $this->options['timeout'] = $timeout;
        return $this;
    }

    /**
     * Set Http Request Body
     *
     * @param array $body
     * @return static
     * @author imxieke <oss@live.hk>
     * @date 2025/10/18 00:19:10
     */
    public function withBody(array $body)
    {
        $this->options['body'] = $body;
        return $this;
    }

    /**
     * Set Http GET Request Query
     *
     * @param array $query
     * @return static
     * @author imxieke <oss@live.hk>
     * @date 2025/10/18 00:20:03
     */
    public function withQuery(array $query)
    {
        $this->options['query'] = $query;
        return $this;
    }

    /**
     * Set Http Request Json Body
     *
     * @param array $data
     * @return static
     * @author imxieke <oss@live.hk>
     * @date 2025/10/18 00:20:44
     */
    public function withJson(array $data)
    {
        $this->options['json'] = $data;
        return $this;
    }

    /**
     * Throw Error ?
     *
     * @param bool $error
     * @return static
     * @author imxieke <oss@live.hk>
     * @date 2025/10/18 00:23:22
     */
    public function withError(bool $error = true)
    {
        $this->options[RequestOptions::HTTP_ERRORS] = $error;
        return $this;
    }

    public function withBasicAuth(string $username, string $password)
    {
        $this->options['auth'] = [$username, $password];
        return $this;
    }

    public function withDigestAuth(string $username, string $password)
    {
        $this->options['auth'] = [$username, $password, 'digest'];
        return $this;
    }

    public function withUserAgent(string $userAgent)
    {
        $this->options['headers']['User-Agent'] = $userAgent;
        return $this;
    }

    /**
     * Set Client Options
     *
     * @param array $options
     * @return static
     * @author imxieke <oss@live.hk>
     * @date 2025/10/18 00:30:44
     */
    public function withOptions(array $options)
    {
        $this->options = array_merge($this->options, $options);
        return $this;
    }

    public function withCookies(array $cookies)
    {
        $this->cookies = $cookies;
        $this->options['cookies'] = $cookies;
        return $this;
    }
}
