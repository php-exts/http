<?php
declare(strict_types=1);

namespace Zeus\Http;

use Zeus\Trait\StatusCode;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use GuzzleHttp\RequestOptions;

use Zeus\Exception\FileNotFoundException;
use Zeus\Exception\InvalidArgumentException;

! defined('CURL_HTTP_VERSION_3') && define('CURL_HTTP_VERSION_3', 3);
! defined('CURL_HTTP_VERSION_3ONLY') && define('CURL_HTTP_VERSION_3ONLY', 3);

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

    public const string METHOD_DELETE = 'DELETE';

    public const string METHOD_GET = 'GET';

    public const string METHOD_HEAD = 'HEAD';

    public const string METHOD_OPTIONS = 'OPTIONS';

    public const string METHOD_PATCH = 'PATCH';

    public const string METHOD_POST = 'POST';

    public const string METHOD_PURGE = 'PURGE';

    public const string METHOD_PUT = 'PUT';

    public const string METHOD_TRACE = 'TRACE';

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

    protected array $authMethods = [
        'basic',
        'digest',
        'ntlm'
    ];

    protected $protocolVersions = [

    ];

    /**
     * Curl Supported Protocol Versions
     *
     * @see https://www.php.net/manual/zh/curl.constants.php#constant.curl-http-version-1-0
     * @var array
     * @author CloudFlying
     * @date 2025/10/19 14:20:55
     */
    protected array $curlProtocolVersions = [
        '1.0'   => CURL_HTTP_VERSION_1_0, // (forces HTTP/1.0)
        '1.1'   => CURL_HTTP_VERSION_1_1, // (forces HTTP/1.1)
        '2'     => CURL_HTTP_VERSION_2, // (alias of CURL_HTTP_VERSION_2_0)
        '2.0'   => CURL_HTTP_VERSION_2_0, // (attempts HTTP 2)
        '2_tls' => CURL_HTTP_VERSION_2TLS, // (attempts HTTP 2 over TLS (HTTPS) only)
        '2pk'   => CURL_HTTP_VERSION_2_PRIOR_KNOWLEDGE, // (issues non-TLS HTTP requests using HTTP/2 without HTTP/1.1 Upgrade).
        '3'     => CURL_HTTP_VERSION_3, // (attempts HTTP 3) php 8.4
        '3o'    => CURL_HTTP_VERSION_3ONLY, // (forces HTTP/3) php 8.4
        '0'     => CURL_HTTP_VERSION_NONE, // (default, lets CURL decide which version to use)
    ];

    /**
     * Curl Supported Method
     *
     * @var array
     * @author CloudFlying
     * @date 2025/10/19 13:40:43
     */
    protected array $curlAuthMethods = [
        'any'          => CURLAUTH_ANY,
        'anysafe'      => CURLAUTH_ANYSAFE,
        'aws_sigv4'    => CURLAUTH_AWS_SIGV4,
        'basic'        => CURLAUTH_BASIC,
        'bearer'       => CURLAUTH_BEARER,
        'digest'       => CURLAUTH_DIGEST,
        'digest_ie'    => CURLAUTH_DIGEST_IE,
        'gssapi'       => CURLAUTH_GSSAPI,
        'gssnegotiate' => CURLAUTH_GSSNEGOTIATE,
        'negotiate'    => CURLAUTH_NEGOTIATE,
        'none'         => CURLAUTH_NONE,
        'ntlm'         => CURLAUTH_NTLM,
        'ntlm_wb'      => CURLAUTH_NTLM_WB,
        'only'         => CURLAUTH_ONLY,
    ];

    /**
     * Response Headers
     *
     * @var array
     * @author CloudFlying
     * @date 2025/10/19 10:48:25
     */
    protected array $responseHeaders = [];

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
     * Verify SSL Info
     *
     * @var bool
     * @author CloudFlying
     * @date 2025/10/18 11:31:59
     */
    protected bool $verifySsl = true;

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
        'allow_redirects' => false,
        'http_errors'     => false,
        'stream'          => false,
        'verify'          => true,
        'debug'           => false,
        'cookies'         => [],
        'headers'         => [
            'User-Agent' => "Zeus/" . PHP_VERSION
        ],
        'cert' => [
            'path' => __DIR__ . '/../../vendor/composer/ca-bundle/res/cacert.pem'
        ],
        'cookie'          => [],
        'version'         => '1.1',
    ];

    /**
     * Response Object
     *
     * @var ResponseInterface
     * @author CloudFlying
     * @date 2025/10/17 19:22:39
     */
    protected ResponseInterface $response;

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
     * Http Client Handle
     *
     * @var object
     */
    protected $client;

    /**
     * Base Uri
     *
     * @param string $uri
     * @return static
     * @author imxieke <oss@live.hk>
     * @date 2025/10/30 10:02:58
     */
    public function setBaseUri(string $uri)
    {
        $this->options['base_uri'] = $uri;

        return $this;
    }

    /**
     * Cert
     *
     * @param array $options
     * @throws InvalidArgumentException
     * @return static
     * @author imxieke <oss@live.hk>
     * @date 2025/10/29 10:16:28
     */
    public function withCert(array $options)
    {
        if (!empty($options['path'])) {
            $options['path'] = realpath($options['path']);
            if (!file_exists($options['path'])) {
                throw new InvalidArgumentException("Cert Not Found: {$options['path']}");
            }
        }

        $this->options['cert'] = [
            $options['path'],
            $options['password'] ?? ''
        ];
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
     * Set Multiple Cookies
     *
     * @param array $cookies
     * @return static
     * @author imxieke <oss@live.hk>
     * @date 2025/10/19 13:02:38
     */
    public function withCookies(array $cookies)
    {
        $this->cookies = array_merge($this->cookies, $cookies);
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
        $this->options['debug'] = $debug;
        return $this;
    }

    /**
     * Delay N second before sending the request.
     *
     * @param int|float $delay
     * @return static
     * @author imxieke <oss@live.hk>
     * @date 2025/10/29 10:27:05
     */
    public function withDelay(int|float $delay = 1)
    {
        $this->options['delay'] = $delay;
        return $this;
    }

    /**
     * Force resolve host name to IPv4.
     *
     * @return static
     * @author imxieke <oss@live.hk>
     * @date 2025/10/29 10:28:51
     */
    public function withIpv4()
    {
        $this->options['force_ip_resolve'] = 'v4';
        return $this;
    }

    /**
     * Force resolve host name to IPv6.
     *
     * @return static
     * @author imxieke <oss@live.hk>
     * @date 2025/10/29 10:30:22
     */
    public function withIpv6()
    {
        $this->options['force_ip_resolve'] = 'v6';
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
     * @param string|int|float $value Header Value
     * @return static
     * @author imxieke <oss@live.hk>
     * @date 2025/10/18 00:16:55
     */
    public function withHeader(string $key, string|int|float $value)
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
     * Bearer Authorization
     *
     * @param string $token
     * @return static
     * @author imxieke <oss@live.hk>
     * @date 2025/10/18 11:29:23
     */
    public function withBearerToken(string $token)
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
        $this->options['connect_timeout'] = $timeout;
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
     * Sets the body of the request to a multipart/form-data form.
     *
     * @param array $form
     * @return static
     * @author imxieke <oss@live.hk>
     * @date 2025/10/29 10:34:27
     */
    public function withMultipart(array $form)
    {
        $this->options['multipart'] = $form;
        return $this;
    }

    /**
     * Used to send an application/x-www-form-urlencoded POST request.
     *
     * @param array $form
     * @return static
     * @author imxieke <oss@live.hk>
     * @date 2025/10/29 10:35:25
     */
    public function withForm(array $form)
    {
        $this->options['form_params'] = $form;
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
    public function withErrors(bool $error = true)
    {
        $this->options['http_errors'] = $error;
        return $this;
    }

    /**
     * 启用时会将响应体作为数据流输出。
     *
     * @author imxieke <oss@live.hk>
     * @param bool $stream
     * @return $this
     */
    public function withStream(bool $stream = true)
    {
        $this->options['stream'] = $stream;
        return $this;
    }

    /**
     * Basic Authentication
     *
     * @see http://www.ietf.org/rfc/rfc2069.txt
     * @param string $username
     * @param string $password
     * @return static
     * @author imxieke <oss@live.hk>
     * @date 2025/10/19 13:36:37
     */
    public function withAuthByBasic(string $username, string $password)
    {
        $this->options['auth'] = [$username, $password, 'basic'];
        return $this;
    }

    /**
     * Digest Authentication
     *
     * @see http://www.ietf.org/rfc/rfc2069.txt
     * @param string $username
     * @param string $password
     * @return static
     * @author imxieke <oss@live.hk>
     * @date 2025/10/19 13:36:01
     */
    public function withAuthByDigest(string $username, string $password)
    {
        $this->options['auth'] = [$username, $password, 'digest'];
        return $this;
    }

    /**
     * Microsoft NTLM authentication
     *
     * @see https://learn.microsoft.com/zh-cn/windows/win32/secauthn/microsoft-ntlm
     * @param string $username
     * @param string $password
     * @return static
     * @author imxieke <oss@live.hk>
     * @date 2025/10/19 13:35:24
     */
    public function withAuthByNtlm(string $username, string $password)
    {
        $this->options['auth'] = [$username, $password, 'ntlm'];
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
     * Set Http Protocol  Version
     *
     * @param string $version
     * @return static
     * @author imxieke <oss@live.hk>
     * @date 2025/10/19 14:15:35
     */
    public function withProtocolVersion(string $version)
    {
        $this->options['version'] = $version;
        return $this;
    }

    /**
     * Allow Redirect
     *
     * @param array $options
     * @return object
     */
    public function withRedirect(array $options): object
    {
        $this->options['allow_redirects'] = [
            'max'             => $options['max'] ?? 3,
            'strict'          => $options['strict'] ?? false,
            'referer'         => $options['referer'] ?? false,
            'protocols'       => $options['protocols'] ?? ['http', 'https'],
            'track_redirects' => $options['track_redirects'] ?? false
        ];
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
     * Query Response Header
     *
     * @param string $key
     * @return array|mixed|string
     * @author imxieke <oss@live.hk>
     * @date 2025/10/19 11:07:20
     */
    public function getHeader(string $key = '')
    {
        if(empty($key)) {
            return $this->responseHeaders;
        }
        $key = strtolower(trim($key));
        return $this->responseHeaders[$key] ?? '';
    }

    /**
     * Query Response Body
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
     * Query Response Status Code
     *
     * @return int
     * @author imxieke <oss@live.hk>
     * @date 2025/10/17 19:31:33
     */
    public function getStatusCode(): int
    {
        return $this->response->getStatusCode();
    }

    /**
     * Get Response Contents
     *
     * @return string
     * @author imxieke <oss@live.hk>
     * @date 2025/10/17 19:32:36
     */
    public function getContents(): string
    {
        return $this->response->getBody()->getContents();
    }

    /**
     * Result Convert to String
     *
     * @return string
     * @author imxieke <oss@live.hk>
     * @date 2025/10/17 19:27:44
     */
    public function __toString()
    {
        return $this->response->getBody()->getContents();
    }
}
