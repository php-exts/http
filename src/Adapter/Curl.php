<?php

declare(strict_types=1);

namespace Zeus\Http\Adapter;

use Zeus\Exception\HttpException;
use Zeus\Exception\ProxyTypeErrorException;
use Zeus\Exception\MethodNotFoundException;
use Zeus\Exception\InvalidParamException;

/**
 * Curl Client
 * #TODO retry upload down save
 * #TODO MultiCurl
 *
 * @author imxieke <oss@live.hk>
 * @copyright (c) 2024 CloudFlying
 */
class Curl
{
    /**
     * Form Data
     *
     * @var mixed
     */
    public mixed $data;

    /**
     * Return data
     *
     * @access protected
     */
    public $res;

    /**
     * Url
     * @var string
     */
    public string $url = '';

    /**
     * 当前请求方法类型 默认为 GET
     *
     * @access public
     * @var string
     */
    public string $method = 'GET';

    /**
     * Support Method
     * 501 Not Implemented
     * 'LINK', 'UNLINK', 'PURGE'
     * @var array
     */
    public array $methods = [
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
     * Curl http Status Code
     * @var int
     */
    protected int $httpCode = 200;

    /**
     * Curl Request Information
     * @var array
     * @access protected
     */
    protected array $info = [];

    /**
     * Curl Error Message
     * @var string
     */
    protected string $error = '';

    /**
     * Curl Error Code
     * @var int
     */
    protected int $errno = 0;

    /**
     * UserAgent Zeus-<spai>/Version
     * @var string
     */
    protected string $userAgent = 'Zeus/' . PHP_VERSION;

    /**
     * 传入参数列表
     * @var array
     */
    public $params = [];

    /**
     * Timeout
     * @var int
     */
    public int $timeout = 5;

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
     * Char Defautl Encoding
     * @var string
     */
    public string $encoding = 'UTF-8';

    /**
     * Cookie
     *
     * @var string|array
     */
    public $cookie;

    /**
     * Encoding Body
     * @var string
     */
    public string $body = '';

    /**
     * Default Http Header
     *
     * @var array
     */
    protected $headers = [
        'Accept: application/json',
        'User-Agent: Zeus/' . PHP_VERSION,
    ];

    /**
     * POST 字段
     *
     * @var array
     */
    public array $postField = [];

    /**
     * 请求响应数据集
     *
     * @access public
     * @var array
     */
    public array $responses = [];

    /**
     * 请求响应数据
     *
     * @access public
     * @var string
     */
    public string $response = '';

    /**
     * Support Proxy Protocols
     *
     * @access protected
     * @var array
     */
    protected $proxyProtocols = [
        'HTTP' => CURLPROXY_HTTP,
        'HTTPS' => CURLPROXY_HTTP,
        'SOCKS4' => CURLPROXY_SOCKS4,
        'SOCKS4A' => CURLPROXY_SOCKS4A,
        'SOCKS5' => CURLPROXY_SOCKS5
    ];

    /**
     * Curl Client Handle
     *
     * @access protected
     */
    protected \CurlHandle $curl;

    public function __construct(string $url)
    {
        $this->init();
    }

    /**
     * Instance Curl
     *
     * @author imxieke <oss@live.hk>
     * @copyright (c) 2024 CloudFlying
     * @return void
     */
    protected function init()
    {
        if (!function_exists('curl_init')) {
            throw new \Exception("Curl Client Extension Not Found", 1);
        }
        $this->curl = curl_init();
    }

    /**
     * Return the all options for current curl ressource
     *
     * @see http://php.net/curl_getinfo
     * @return array
     */
    public function getOpts()
    {
        return curl_getinfo($this->curl);
    }

    /**
     * Set customized curl options.
     *
     * To see a full list of options: http://php.net/curl_setopt
     *
     * @see http://php.net/curl_setopt
     * @param int $option The curl option constant e.g. `CURLOPT_AUTOREFERER`, `CURLOPT_COOKIESESSION`
     * @param mixed $value The value to pass for the given $option
     * @return $this
     */
    public function setOpt(int $option, mixed $value)
    {
        curl_setopt($this->curl, $option, $value);
        return $this;
    }

    /**
     * Set Cookie (Will be Overwrite $this->cookie from request method($url,$data,$headers))
     *
     * string: "key=val;key2=val2;key3=val3"
     * array ['key' => 'val', 'key2' => 'val2', 'key3' => 'val3']
     * @param string $cookie cookie string or array or cookie file
     * @author imxieke <oss@live.hk>
     * @copyright (c) 2024 CloudFlying
     */
    public function cookie(string|array $cookie)
    {
        if (is_file($cookie)) {
            $cookie = realpath($cookie);
            $this->cookie = file_get_contents($cookie);
            $this->setOpt(CURLOPT_COOKIEFILE, $cookie);
        } elseif (is_array($cookie)) {
            $this->cookie = implode(';', $cookie);
        } else {
            $this->cookie = $cookie;
        }
        $this->setOpt(CURLOPT_COOKIE, $cookie);
        return $this;
    }

    /**
     * timeout
     *
     * @param int $timeout Timeout time ,unit second
     * @return object
     */
    public function timeout(int $timeout = 5)
    {
        $this->setOpt(CURLOPT_TIMEOUT, $timeout);
        $this->setOpt(CURLOPT_CONNECTTIMEOUT, $timeout);
        return $this;
    }

    /**
     * HTTP Basic Authentication.
     *
     * @param string $username
     * @param string $password
     * @return self
     */
    public function basicAuth(string $username, string $password)
    {
        $this->setOpt(CURLOPT_USERPWD, "$username:$password");
        return $this;
    }

    /**
     * Set a proxy server for outgoing requests to tunnel through.
     * Http , Socks4 Socks4a Socks5
     *
     * @param string $server    hostname
     * @param int $port         8080
     * @param string $auth      user:passwd
     * @param string $type      default is CURLPROXY_HTTP
     * @return self
     */
    public function proxy(string $server, int $port, string $type = 'HTTP', string $auth = '')
    {
        $this->setOpt(CURLOPT_PROXY, $server);
        $type = \strtoupper($type);

        if (isset($this->proxyProtocols[$type])) {
            $this->setOpt(CURLOPT_PROXYTYPE, $this->proxyProtocols[$type]);
            // $this->setOpt(CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
        } else {
            throw new ProxyTypeErrorException("Unknown Proxy Type: {$type}", 1);
        }

        if ($port) {
            $this->setOpt(CURLOPT_PROXYPORT, $port);
        }

        if ($auth != '') {
            $this->setOpt(CURLOPT_PROXYAUTH, CURLAUTH_BASIC);
            $this->setOpt(CURLOPT_USERPWD, $auth);
        }
        return $this;
    }

    /**
     *
     * Allow Max Redirect times
     * @param int $maxRedirects TRUE OR FALSE
     * @return $this
     */
    public function maxRedirects(int $maxRedirects = 3): object
    {
        $this->maxRedirects = $maxRedirects;
        return $this;
    }

    /**
     *
     * Allow Max Redirect times
     * @param bool $follow bool
     * @return self
     */
    public function followLocation(bool $follow = false)
    {
        $this->setOpt(CURLOPT_FOLLOWLOCATION, $follow);
        return $this;
    }

    /**
     * Set Http Header
     * Format ['Content-type: application/json']
     * Error: ['Content-type' => 'application/json']
     * priority: header($headers) > method($url,$data,$headers) params
     *
     * @author imxieke <oss@live.hk>
     * @param array $headers
     * @return object
     */
    public function header(array $headers = []): object
    {
        $this->headers = (count($headers) > 0) ? $headers : $this->headers;
        $this->setOpt(CURLOPT_HTTPHEADER, $this->headers);
        return $this;
    }

    /**
     * 启用时会将头文件的信息作为数据流输出。
     *
     * @author imxieke <oss@live.hk>
     * @param bool $show
     * @return $this
     */
    public function withHeader(bool $show = false)
    {
        $this->setOpt(CURLOPT_HEADER, $show);
        return $this;
    }

    /**
     * Provide a User Agent.
     *
     * In order to provide you customized user agent name you can use this method.
     * @param string $useragent The name of the user agent to set for the current request
     * @return self
     */
    public function userAgent(?string $useragent = null)
    {
        $this->setOpt(CURLOPT_USERAGENT, $useragent ?? $this->userAgent);
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
    public function referer($referer)
    {
        $this->setOpt(CURLOPT_REFERER, $referer);
        return $this;
    }

    /**
     * Enable verbosity.
     *
     * @param bool $status
     * @return self
     */
    public function verbose(bool $status = true)
    {
        $this->setOpt(CURLOPT_VERBOSE, $status);
        return $this;
    }

    /**
     * Http Version
     *
     * CURL_HTTP_VERSION_NONE (default, lets CURL decide which version to use)
     * CURL_HTTP_VERSION_1_0 (forces HTTP/1.0)
     * CURL_HTTP_VERSION_1_1 (forces HTTP/1.1)
     * CURL_HTTP_VERSION_2_0 (attempts HTTP 2)
     * CURL_HTTP_VERSION_2 (alias of CURL_HTTP_VERSION_2_0)
     * CURL_HTTP_VERSION_2TLS (attempts HTTP 2 over TLS (HTTPS) only)
     * CURL_HTTP_VERSION_2_PRIOR_KNOWLEDGE (issues non-TLS HTTP requests using HTTP/2 without HTTP/1.1 Upgrade).
     * @param int $version
     * @return $this
     */
    public function setHttpVersion(int $version = CURL_HTTP_VERSION_NONE)
    {
        $this->setOpt(CURLOPT_HTTP_VERSION, $version);
        return $this;
    }

    /**
     * 编码转换
     * @param string $body 需要转换的内容
     * @param string $from Set string source encoding
     * @param string $to Set string new encoding
     * @return $this
     */
    function encoding(string $body, ?string $from = null, ?string $to = null)
    {
        $to = $to ?? $this->encoding;
        $from = $from ?? mb_check_encoding($body);
        $this->body = mb_convert_encoding($body, $to, $from);
        return $this;
    }

    /**
     *
     * Allow Redirect
     * @param $redirect true or false, default is false
     * @return object
     */
    public function allowRedirect(bool $redirect = false): object
    {
        $this->allowRedirect = $redirect;
        return $this;
    }

    /**
     * 定义请求方法参数
     * GET 请求会自动添加 ？连接符
     * POST 等传送的数据需要在调用 method 前设定
     *
     * @param string $method
     * @return $this|HttpException
     */
    public function method(string $method)
    {
        $method = strtoupper(trim($method));
        // if (is_array($this->data) && count($this->data) > 0) {
        // $this->data = [];
        // }
        $this->setOpt(CURLOPT_URL, $this->url);
        // Disable Direct Output Body To Console
        $this->setOpt(CURLOPT_RETURNTRANSFER, true);
        switch ($method) {
            case 'GET':
                $this->setOpt(CURLOPT_HTTPGET, true);
                // 是否以 / 结尾
                $suffix = substr($this->url, -1, 1);
                // $connector = $suffix == '/' ? '?' : '/?';
                $connector = '?';
                if (is_array($this->data) && count($this->data) > 0) {
                    $suffix = $connector . http_build_query($this->data);
                    $this->setOpt(CURLOPT_URL, $this->url . $suffix);
                }
                break;
            case 'POST':
                $this->setOpt(CURLOPT_POST, true);
                $this->setOpt(CURLOPT_POSTFIELDS, $this->data);
                break;
            case 'HEAD':
                // head nobody request need
                $this->setOpt(CURLOPT_NOBODY, true);
                $this->setOpt(CURLOPT_CUSTOMREQUEST, 'HEAD');
                break;
            case 'DELETE':
                $this->setOpt(CURLOPT_CUSTOMREQUEST, 'DELETE');
                $this->setOpt(CURLOPT_POSTFIELDS, $this->data);
                break;
            case 'PUT':
                $this->setOpt(CURLOPT_CUSTOMREQUEST, 'PUT');
                $this->setOpt(CURLOPT_POSTFIELDS, $this->data);
                break;
            case 'OPTIONS':
                $this->setOpt(CURLOPT_CUSTOMREQUEST, 'OPTIONS');
                $this->setOpt(CURLOPT_POSTFIELDS, $this->data);
                break;
            case 'CONNECT':
                $this->setOpt(CURLOPT_CUSTOMREQUEST, 'CONNECT');
                break;
            case 'TRACE':
                $this->setOpt(CURLOPT_CUSTOMREQUEST, 'TRACE');
                break;
            case 'PATCH':
                $this->setOpt(CURLOPT_CUSTOMREQUEST, 'PATCH');
                break;
            default:
                throw new HttpException("Unknow Http Request Method $method", 1);
        }
        return $this;
    }

    /**
     * 是否验证 SSL 证书
     * @param bool $verify
     * @return $this
     */
    public function ssl(bool $verify = true)
    {
        $this->setOpt(CURLOPT_SSL_VERIFYPEER, $verify);
        // $this->setOpt(CURLOPT_SSL_VERIFYHOST, $verify ? 2 : 0);
        return $this;
    }

    /**
     * return Request Body
     *
     * @return bool|string
     * @author imxieke <oss@live.hk>
     * @copyright (c) 2024 CloudFlying
     */
    public function body()
    {
        return $this->res;
    }

    /**
     * Return Request Info
     *
     * @param string $key 获取指定 key 的信息
     * @author imxieke <oss@live.hk>
     * @copyright (c) 2024 CloudFlying
     * @return array|string
     */
    public function info(string $key)
    {
        if (!empty($this->info[$key])) {
            return $this->info[$key];
        }
        return $this->info;
    }

    /**
     * Return Error When has error will return error msg
     * @return string
     * @author imxieke <oss@live.hk>
     * @copyright (c) 2024 CloudFlying
     */
    public function error()
    {
        return $this->errno . '' . $this->error;
    }

    /**
     * Send Request
     *
     * @author imxieke <oss@live.hk>
     * @return $this|string|array
     */
    public function send()
    {
        if (empty($this->url)) {
            throw new InvalidParamException("Url Can't be null", 1);
        }
        $this->ssl();
        $this->maxRedirects();
        $this->allowRedirect();
        $this->header();
        $this->timeout();
        $this->withHeader();
        $this->res = curl_exec($this->curl);
        $this->errno = curl_errno($this->curl);
        $this->error = curl_error($this->curl);
        $this->info = curl_getinfo($this->curl);
        $this->close();
        return $this;
    }

    /**
     * 发起请求
     *
     * #TODO CURLOPT_COOKIEFILE
     * #TODO CURLOPT_COOKIEJAR
     * #TODO CURLOPT_RETURNTRANSFER bool
     * #TODO CURLOPT_ENCODING
     * #TODO CURLOPT_NOBODY
     *
     * @param string $method Request Method
     * @param string $url    Request Url
     * @param array $headers  Request Params
     * @param mixed $data    Request Data array|json
     * @return $this
     * @author imxieke <oss@live.hk>
     * @copyright (c) 2024 CloudFlying
     */
    public function request(string $method, string $url, mixed $data = [], array $headers = [])
    {
        $this->headers = (count($headers) > 0) ? $headers : $this->headers;
        $this->url = trim($url);
        $this->data = $data;
        $this->method($method);
        $this->send();
        return $this;
    }

    /**
     * Closing the current open curl resource.
     * @return void
     */
    public function close()
    {
        curl_close($this->curl);
    }

    /**
     * Curl Client Version
     *
     * @return array|bool
     */
    public function version(): array
    {
        return curl_version();
    }

    /**
     * Http Request Method
     * @param mixed $method
     * @param mixed $args
     * @return array|string|object|$this
     * @author imxieke <oss@live.hk>
     * @copyright (c) 2024 CloudFlying
     */
    public function __call($method, $args)
    {
        $method = strtoupper(\trim($method));
        if (in_array($method, $this->methods)) {
            $this->url = $args[0] ?? '';
            $this->data = $args[1] ?? [];
            $this->headers = $args[2] ?? $this->headers;
            return $this->request($method, $this->url, $this->data, $this->headers);
        } else {
            throw new MethodNotFoundException("Unknow Method $method", 1);
        }
    }
}
