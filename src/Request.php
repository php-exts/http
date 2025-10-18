<?php
declare(strict_types=1);

namespace Zeus\Http;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\RequestInterface;
use Zeus\Exception\MethodNotFoundException;

/**
 * Request
 *
 * @author imxieke <oss@live.hk>
 * @copyright (c) 2025 CloudFlying
 * @date 2025/10/17 17:18:25
 */
class Request
{
    /**
     * 请求对象
     *
     * @access protected
     * @var object
     */
    protected $request;

    /**
     * 允许请求的类型
     *
     * @author imxieke <oss@live.hk>
     * @var array
     */
    protected array $requestType = [
        'XML',
        'JSON',
        'JSONP',
        'TRACE',
        'AJAX',
        'PJAX',
        'XML',
        'DOWNLOAD',
        'UPLOAD'
    ];

    /**
     * 获取 GET 请求参数
     *
     * @access protected
     * @var array
     */
    protected array $get = [];

    /**
     * 获取 POST 请求参数
     *
     * @access protected
     * @var array
     */
    protected array $post = [];

    /**
     * 获取用户请求参数
     *
     * @access protected
     * @var mixed
     */
    protected mixed $input = [];

    /**
     * 获取 $_ENV 参数
     *
     * @access protected
     * @var array
     */
    protected array $env = [];

    /**
     * 获取 $_FILES 参数
     *
     * @access protected
     * @var array
     */
    protected array $file = [];

    /**
     * 获取 $_COOKIE 参数
     *
     * @access protected
     * @var array
     */
    protected array $cookie = [];

    /**
     * 获取 $_SESSION 参数
     *
     * @access protected
     * @var array
     */
    protected array $session = [];

    /**
     * 获取 $_SERVER header 参数
     *
     * @access protected
     * @var array
     */
    protected array $header = [];

    /**
     * 获取 $_SERVER 参数
     *
     * @access protected
     * @var array
     */
    protected array $server = [];

    public function __construct($request = null)
    {
        $this->request = $request;
        $this->input = file_get_contents('php://input');
        $this->get = $_GET ?? [];
        $this->post = $_POST ?? $this->input;
        $this->server = $_SERVER ?? [];
        $this->env = $_ENV ?? [];
        $this->file = $_FILES ?? [];
        $this->cookie = $_COOKIE ?? [];
        $this->session = $_SESSION ?? [];
        $this->header = $_SERVER ?? [];
    }

    /**
     * 获取客户端浏览器类型
     *
     * @author imxieke <oss@live.hk>
     * @return string
     */
    public function browser()
    {
        $Browser = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
        if (preg_match('/MSIE/i', $Browser)) {
            $Browser = 'MSIE';
        } elseif (preg_match('/Firefox/i', $Browser)) {
            $Browser = 'Firefox';
        } elseif (preg_match('/Chrome/i', $Browser)) {
            $Browser = 'Chrome';
        } elseif (preg_match('/Safari/i', $Browser)) {
            $Browser = 'Safari';
        } elseif (preg_match('/Opera/i', $Browser)) {
            $Browser = 'Opera';
        } else {
            $Browser = 'Other';
        }
        return $Browser;
    }

    /**
     * 获取输入的值
     *
     * @author imxieke <oss@live.hk>
     * @param string $name 可选 Cookie 名
     * @return string|array
     */
    public function input(string $name = '')
    {
        if ($name != '') {
            return $this->input[$name] ?? '';
        }
        return $this->input;
    }

    /**
     * 获取 Cookie 信息
     *
     * @author imxieke <oss@live.hk>
     * @param string $name 可选 Cookie 名
     * @return string|array
     */
    public function cookie(string $name = '')
    {
        if ($name != '') {
            return $this->cookie[$name] ?? '';
        }
        return $this->cookie;
    }

    /**
     * 获取 Session 信息
     * @author imxieke <oss@live.hk>
     * @param string $name 可选 Session 名
     * @return string|array
     */
    public function session(string $name = '')
    {
        if ($name != '') {
            return $this->session[$name] ?? '';
        }
        return $this->session;
    }

    /**
     * 获取上传的文件
     *
     * @author imxieke <oss@live.hk>
     * @param string $name 可选 文件名
     * @return array|string
     */
    public function file(string $name = '')
    {
        if ($name != '') {
            return $this->file[$name] ?? '';
        }
        return $this->file;
    }

    /**
     * 获取服务器信息
     *
     * @author imxieke <oss@live.hk>
     * @param string $name 可选 服务器头部信息名
     * @return array|string
     */
    public function server(string $name = ''): mixed
    {
        if ($name != '') {
            return $this->server[$name] ?? '';
        }
        return $this->server;
    }

    /**
     * 获取 header 信息
     *
     * @author imxieke <oss@live.hk>
     * @param string $name 可选 header 信息名
     * @return string|array
     */
    public function header(?string $name = null, mixed $default = null)
    {
        if ($name != '') {
            return $this->header[$name] ?? $default;
        }
        return $this->header;
    }

    /**
     * 获取 $_ENV 参数
     *
     * @author imxieke <oss@live.hk>
     * @param string $name 可选 $_ENV 参数名
     * @return string|array
     */
    public function env(string $name = '')
    {
        if ($name != '') {
            return $this->env[$name] ?? '';
        }
        return $this->env;
    }

    /**
     * 获取浏览器所使用的语言
     * 通过 explode('','') 分割 en-US,en;q=0.9,zh-CN;q=0.8,zh;q=0.7
     * 只取前4位，仅判断优先的语言。如果取前5位，可能出现en,zh的情况，影响判断。
     * @return string
     */
    public function lang()
    {
        $lang = substr(\strtolower($this->server('HTTP_ACCEPT_LANGUAGE')), 0, 5);
        list($prefix, $suffix) = explode('-', $lang);
        $lang = \str_replace('-', '_', $lang);
        return \strtolower($prefix) . '_' . \strtoupper($suffix);
    }







    /**
     * 获取用户代理
     * @return string|array
     */
    public function userAgent()
    {
        return $this->server('HTTP_USER_AGENT');
    }

    /**
     * 获取端口
     * @return string
     */
    public function port()
    {
        return $this->server('SERVER_PORT');
    }

    /**
     * 获取当前请求 URL 的 pathinfo 信息
     * @return string
     */
    public function pathinfo()
    {
        return $this->server('PATH_INFO');
    }

    /**
     * 当前URL的访问后缀
     * @access public
     * @return string
     */
    public function ext()
    {
        return pathinfo($this->pathinfo(), PATHINFO_EXTENSION);
    }

    /**
     * 获取客户端 端口
     * @return string
     */
    public function remotePort()
    {
        return $this->server('REMOTE_PORT');
    }

    /**
     * Http 协议
     * HTTP/1.0 HTTP/1.1 HTTP/2
     * @return string|int
     */
    public function protocol()
    {
        return $this->server('SERVER_PROTOCOL');
    }

    /**
     * Http 协议
     * HTTP/1.0 HTTP/1.1 HTTP/2
     * @return string|int
     */
    public function uri()
    {
        return $this->server('REQUEST_URI');
    }

    /**
     * Http 请求方法
     * @return string|int
     */
    public function method()
    {
        return $this->server('REQUEST_METHOD');
    }

    /**
     * 域名
     * @return string|int
     */
    public function domain()
    {
        return $this->server('SERVER_NAME');
    }

    /**
     * Host
     * @return string|int
     */
    public function host()
    {
        return $this->server('HTTP_HOST');
    }

    /**
     * Compress Protocol
     * @return string
     */
    public function encode()
    {
        return $this->server('HTTP_ACCEPT_ENCODING');
    }

    /**
     * Client Request Time
     * @return string|int
     */
    public function time()
    {
        return $this->server('REQUEST_TIME');
    }

    /**
     * 获取 Web 服务器信息
     * @return string
     */
    public function webServer()
    {
        return $this->server('SERVER_SOFTWARE');
    }

    /**
     * 是否为 HTTPS
     * @return bool
     */
    public function isSsl()
    {
        if ($this->port() == '443') {
            return true;
        } elseif (stristr($this->protocol(), 'https') !== false) {
            return true;
        } elseif (\strtolower($this->server('HTTPS')) == 'on') {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 当前URL地址中的 scheme 参数
     * @access public
     * @return string
     */
    public function scheme(): string
    {
        return $this->isSsl() ? 'https' : 'http';
    }

    /**
     * 当前请求URL地址中的query参数
     * @access public
     * @return string
     */
    public function query(): string
    {
        return $this->server('QUERY_STRING');
    }

    /**
     * 获取访客 IP 地址
     *
     * @suppress PHP0418
     * @return string
     */
    public static function ip()
    {
        $realIp = '0.0.0.0';
        // 兼容 Webman
        if (class_exists('\Webman\Http\Request')) {
            $realIp = request()->getRealIp();
        }

        $headers = [
            "True-Client-IP",           // CloudFlare Enterprise plan only
            "HTTP_CF_CONNECTING_IP",    // CloudFlare CDN 若使用 Cloudflare REMOTE_ADDR 是不正确的 为 节点 IP
            "HTTP_X_FORWARDED_FOR",     // 包含代理的真实 IP 如果不使用代理访问则为空
            "REMOTE_ADDR",              // 可隐藏 不可伪造 最后握手的 IP
            "HTTP_CLIENT_IP",           // 可伪造
            "X-Forwarded-For",          // 可伪造 访客通过节点所有 IP 集合
            "HTTP_CDN_SRC_IP",
            "HTTP_PROXY_CLIENT_IP",
            "HTTP_WL_PROXY_CLIENT_IP",
            "HTTP_X_CLUSTER_CLIENT_IP",
            "HTTP_X_FORWARDED",         // 包含代理的真实IP
            "HTTP_FORWARDED_FOR",       // 包含代理的真实IP
            "CF-Connecting-IP",         // CloudFlare CDN 连接到的服务器 IP
            "HTTP_VIA",
            "X-Real-IP",                // CDN
            "CLIENT-IP",                // 可伪造
        ];

        $ip = '';
        foreach ($headers as $header) {
            if (isset($_SERVER[$header]) && strtolower($_SERVER[$header]) != '') {
                $ip = $_SERVER[$header];
                break;
            }
        }

        /* 若结果为 X-Forwarded-For 的值 则取出最后一个 IP */
        if ($pos = strpos($ip, ',')){
            $ip = substr($ip, $pos + 1);
        }
        if (empty($ip)) {
            return $realIp;
        }
        return $ip;
    }

    /**
     * Get all HTTP header key/values as an associative array for the current request.
     *
     * @return string|array|array[string] The HTTP header key/value pairs.
     */
    public static function getallheaders()
    {
        $headers = [];

        $copy_server = [
            'CONTENT_TYPE' => 'Content-Type',
            'CONTENT_LENGTH' => 'Content-Length',
            'CONTENT_MD5' => 'Content-Md5',
        ];

        foreach ($_SERVER as $key => $value) {
            if (substr($key, 0, 5) === 'HTTP_') {
                $key = substr($key, 5);
                if (!isset($copy_server[$key]) || !isset($_SERVER[$key])) {
                    $key = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', $key))));
                    $headers[$key] = $value;
                }
            } elseif (isset($copy_server[$key])) {
                $headers[$copy_server[$key]] = $value;
            }
        }

        if (!isset($headers['Authorization'])) {
            if (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
                $headers['Authorization'] = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
            } elseif (isset($_SERVER['PHP_AUTH_USER'])) {
                $basic_pass = isset($_SERVER['PHP_AUTH_PW']) ? $_SERVER['PHP_AUTH_PW'] : '';
                $headers['Authorization'] = 'Basic ' . base64_encode($_SERVER['PHP_AUTH_USER'] . ':' . $basic_pass);
            } elseif (isset($_SERVER['PHP_AUTH_DIGEST'])) {
                $headers['Authorization'] = $_SERVER['PHP_AUTH_DIGEST'];
            }
        }
        return $headers;
    }

    /**
     * 是否为GET请求
     * @access public
     * @return bool
     */
    public function isGet(): bool
    {
        return $this->method() == 'GET';
    }

    /**
     * 是否为POST请求
     * @access public
     * @return bool
     */
    public function isPost(): bool
    {
        return $this->method() == 'POST';
    }

    /**
     * 是否为PUT请求
     * @access public
     * @return bool
     */
    public function isPut(): bool
    {
        return $this->method() == 'PUT';
    }

    /**
     * 是否为DELTE请求
     * @access public
     * @return bool
     */
    public function isDelete(): bool
    {
        return $this->method() == 'DELETE';
    }

    /**
     * 是否为HEAD请求
     * @access public
     * @return bool
     */
    public function isHead(): bool
    {
        return $this->method() == 'HEAD';
    }

    /**
     * 是否为PATCH请求
     * @access public
     * @return bool
     */
    public function isPatch(): bool
    {
        return $this->method() == 'PATCH';
    }

    /**
     * 是否为OPTIONS请求
     * @access public
     * @return bool
     */
    public function isOptions(): bool
    {
        return $this->method() == 'OPTIONS';
    }

    /**
     * 是否为cli
     * @access public
     * @return bool
     */
    public function isCli(): bool
    {
        return PHP_SAPI == 'cli';
    }

    /**
     * 是否为cgi
     * str_starts_with from symfony/polyfill
     * @access public
     * @return bool
     */
    public function isCgi(): bool
    {
        return str_starts_with(PHP_SAPI, 'cgi');
    }

    /**
     * 检测是否为移动端设备
     *
     * @return bool
     */
    public function isWap()
    {
        if (
            isset($_SERVER['HTTP_USER_AGENT']) &&
            preg_match(
                '/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|iphone|ipad|ipod|android|xoom)/i',
                strtolower($_SERVER['HTTP_USER_AGENT'])
            )
        )
            return true;
        if (
            (isset($_SERVER['HTTP_ACCEPT'])) &&
            (strpos(strtolower($_SERVER['HTTP_ACCEPT']), 'application/vnd.wap.xhtml+xml') !== false)
        )
            return true;

        return false;
    }

    /**
     * 获取 Bearer Token
     *
     * @return bool|string
     * @author imxieke <oss@live.hk>
     * @date 2025/07/20 23:51:35
     */
    public function getBearerToken()
    {
        $header = $this->header('Authorization', '');
        $position = strripos($header, 'Bearer ');

        if($position !== false) {
            $header = substr($header, $position + 7);

            return str_contains($header, ',') ? strstr($header, ',', true) : $header;
        }
        return '';
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
        // Http Request
        if (in_array($method, $this->methods)) {
            return $this->request->$method(...$args)->body();
        } elseif (method_exists($this->request, $method)) {
            return $this->request->$method(...$args);
        } else {
            throw new MethodNotFoundException("Unknow Method $method", 1);
        }
    }
}
