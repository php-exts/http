<?php
declare(strict_types=1);

namespace Zeus\Http;

use Zeus\Http\StatusCode;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\RequestOptions;

use Zeus\Exception\FileNotFoundException;

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
     * @author CloudFlying
     * @date 2025/10/18 10:26:00
     */
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
     * 当前请求方法类型 默认为 GET
     *
     * @access public
     * @var string
     */
    public string $method = 'GET';

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

    /**
     * Request Url
     *
     * @var string
     */
    protected string $url;

    /**
     * Http Cookies
     *
     * @var array
     * @author CloudFlying
     * @date 2025/10/17 09:56:43
     */
    protected array $cookies = [];

    /**
     * Set Max Redirect times
     * @var int
     */
    public int $maxRedirects = 3;

    /**
     * Allow Redirect
     * @var bool
     */
    public bool $allowRedirect = false;

    /**
     * Follow Redirect
     * @var bool
     */
    public bool $followLocation = false;

    /**
     * Verify SSL Info
     *
     * @var bool
     * @author CloudFlying
     * @date 2025/10/18 11:31:59
     */
    protected bool $verifySsl = true;

    /**
     * More Verbose Message
     *
     * @var bool
     * @author CloudFlying
     * @date 2025/10/18 11:44:10
     */
    protected bool $debug = false;

    /**
     * Http Request Errors
     *
     * @var array
     * @author CloudFlying
     * @date 2025/10/18 09:25:39
     */
    public array $errors = [];

    /**
     * Curl http Status Code
     *
     * @var int
     */
    protected int $httpCode = 200;

    /**
     * Http Options
     *
     * @var array
     * @author CloudFlying
     * @date 2025/10/18 00:06:02
     */
    protected array $options = [
        'base_uri'        => '',
        'timeout'         => 10,
        'allow_redirects' => true,
        'http_errors'     => false,
        'verify'          => true,
        // GET
        'query'           => [],
        // POST
        'json'            => [],
        'body'            => [],
        'headers'         => [
            'User-Agent' => "Zeus/" . PHP_VERSION
        ]
    ];

    /**
     * Result Body Content
     *
     * @var string
     * @author CloudFlying
     * @date 2025/10/18 10:20:23
     */
    public string $body;

    /**
     * Curl Request Information
     *
     * @var array
     * @access protected
     */
    protected array $info = [];

    /**
     * Curl Error Message
     *
     * @var string
     * @access protected
     */
    protected string $error = 'none';

    /**
     * Curl Error Code
     *
     * @var int
     * @access protected
     */
    protected int $errno = 0;

    /**
     * 传入参数列表
     *
     * @var array
     */
    public array $params = [];

    /**
     * 请求传输字段
     *
     * @var array
     */
    public array $fields = [];

    /**
     * Cookie
     *
     * @var string|array
     */
    protected $cookie;

    /**
     * Charset Defautl Encoding
     *
     * @var string
     */
    public string $encoding = 'UTF-8';

    /**
     * Show Header Info (Curl)
     *
     * @var bool
     * @author CloudFlying
     * @date 2025/10/18 11:21:30
     */
    protected bool $showHeaderInfo = false;

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
        $this->options['base_uri'] = $uri;

        return $this;
    }

    /**
     * Set Request Cookie
     *
     * string: "key=val;key2=val2;key3=val3"
     * array ['key' => 'val', 'key2' => 'val2', 'key3' => 'val3']
     *
     * @param string $name Cookie Name
     * @param string $value Cookie Value
     * @return static
     * @author imxieke <oss@live.hk>
     * @date 2025/10/18 11:50:40
     */
    public function withCookie(string $name, string $value)
    {
        $this->cookies[$name] = $value;
        return $this;
    }

    /**
     * Multiple Set Cookie
     *
     * @param array $cookies
     * @return static
     * @author imxieke <oss@live.hk>
     * @date 2025/10/18 11:50:52
     */
    public function withCookies(array $cookies)
    {
        $this->cookies            = array_merge($this->cookies, $cookies);
        $this->options['cookies'] = $cookies;
        return $this;
    }

    /**
     * Set Cookie File
     *
     * @param string $cookieFile
     * @return static
     * @author imxieke <oss@live.hk>
     * @date 2025/10/18 11:56:13
     */
    public function withCookieFile(string $cookieFile)
    {
        $cookieFile = realpath($cookieFile);
        if(! file_exists($cookieFile)) {
            throw new FileNotFoundException("Cookie File Not Found: $cookieFile", 1);
        }
        $this->options['cookie_file'] = $cookieFile;
        return $this;
    }

    /**
     * Show More Verbose Message
     *
     * @param bool $debug
     * @return static
     * @author imxieke <oss@live.hk>
     * @date 2025/10/18 11:45:42
     */
    public function withDebug(bool $debug = true)
    {
        $this->debug = $debug;
        return $this;
    }

    /**
     * Http Request userAgent
     *
     * @param string $userAgent The name of the user agent to set for the current request
     * @return static
     * @author imxieke <oss@live.hk>
     * @date 2025/10/18 11:19:46
     */
    public function withUserAgent(string $userAgent)
    {
        $this->options['headers']['User-Agent'] = $userAgent;
        return $this;
    }

    /**
     * Set Header
     *
     * Format ['Content-type: application/json']
     * Error: ['Content-type' => 'application/json']
     *
     * @param string $key Header Key
     * @param string $value Header Value
     * @return static
     * @author imxieke <oss@live.hk>
     * @date 2025/10/18 00:16:55
     */
    public function withHeader(string $key, string $value)
    {
        $this->options['headers'][$key] = $value;
        return $this;
    }

    /**
     * Mulltiple Set Http Header
     *
     * @param array $headers
     * @return static
     * @author imxieke <oss@live.hk>
     * @date 2025/10/18 11:28:56
     */
    public function withHeaders(array $headers)
    {
        $this->options['headers'] = array_merge($this->options['headers'], $headers);
        return $this;
    }

    /**
     * Set Http Request Bearer Header
     *
     * @param string $token
     * @return static
     * @author imxieke <oss@live.hk>
     * @date 2025/10/18 11:29:23
     */
    public function withBearerHeader(string $token)
    {
        $this->withHeader("Authorization", "Bearer {$token}");
        return $this;
    }

    /**
     * 启用时会将头文件的信息作为数据流输出。
     *
     * @author imxieke <oss@live.hk>
     * @param bool $show
     * @return $this
     */
    public function withHeaderInfo(bool $show = true)
    {
        $this->showHeaderInfo = $show;
        return $this;
    }

    /**
     * Set the HTTP referer header.
     *
     * The $referer Information can help identify the requested client where the requested was made.
     *
     * @param string $referer An url to pass and will be set as referer header
     * @return self
     */
    public function withReferer($referer)
    {
        $this->withHeader('Referer', $referer);
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
    public function withTimeout(int|float $timeout)
    {
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
        $this->withHeader('Content-Type', 'application/json');
        $this->withHeader('Accept', 'application/json');
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
        $this->options['http_errors'] = $error;
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

    /**
     * Follow Location Redirect
     *
     * @param bool $follow
     * @return self
     */
    public function withFollowLocation(bool $follow = false)
    {
        $this->followLocation = $follow;
        return $this;
    }

    /**
     * Allow Redirect
     *
     * @param $redirect true or false, default is false
     * @return object
     */
    public function allowRedirect(bool $redirect = false): object
    {
        $this->allowRedirect = $redirect;
        return $this;
    }

    /**
     * Encoding Convert
     *
     * @param string $body Content need to convert encoding
     * @param string $from Set string source encoding
     * @param string $to Set string new encoding
     * @return $this
     */
    function encoding(string $body, ?string $from = NULL, ?string $to = NULL)
    {
        $to         = $to ?? $this->encoding;
        $from       = $from ?? mb_check_encoding($body);
        $this->body = mb_convert_encoding($body, $to, $from);
        return $this;
    }

    /**
     * Verify SSL
     *
     * @param bool $verify
     * @return static
     * @author imxieke <oss@live.hk>
     * @date 2025/10/18 11:32:34
     */
    public function verifySsl(bool $verify = true)
    {
        $this->verifySsl = $verify;
        return $this;
    }

    /**
     * Allow Max Redirect times
     *
     * @param int $maxRedirects
     * @return $this
     */
    public function setMaxRedirects(int $maxRedirects = 3): object
    {
        $this->maxRedirects = $maxRedirects;
        return $this;
    }

    /**
     * Get Response Content
     *
     * @return string
     * @author imxieke <oss@live.hk>
     * @date 2025/10/18 15:34:31
     */
    public function getContents()
    {
        return $this->body;
    }
}
