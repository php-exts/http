<?php
declare(strict_types=1);

namespace Zeus\Http\Adapter;

use CurlHandle;
use CurlMultiHandle;
use CurlShareHandle;
use CurlSharePersistentHandle;
use CURLFile;
use CURLStringFile;
use Zeus\Http\Adapter;
use Zeus\Exception\HttpException;
use Zeus\Exception\ProxyTypeErrorException;
use Zeus\Exception\MethodNotFoundException;
use Zeus\Exception\InvalidParamException;
use Zeus\Exception\ClassNotFoundException;
use Zeus\Exception\ExtensionNotFoundException;

/**
 * Curl Client
 * #TODO retry upload down save
 * #TODO MultiCurl
 *
 * @author imxieke <oss@live.hk>
 * @copyright (c) 2024 CloudFlying
 */
class Curl extends Adapter
{
    /**
     * Support Proxy Protocols
     *
     * @access protected
     * @var array
     */
    protected $proxyProtocols = [
        'HTTP'    => CURLPROXY_HTTP,
        'HTTPS'   => CURLPROXY_HTTP,
        'SOCKS4'  => CURLPROXY_SOCKS4,
        'SOCKS4A' => CURLPROXY_SOCKS4A,
        'SOCKS5'  => CURLPROXY_SOCKS5
    ];

    /**
     * Curl Client Handle
     *
     * @access protected
     */
    // protected CurlHandle $client;

    public function __construct(array $options = [])
    {
        $this->options = array_merge($this->options, $options);
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
        if(! function_exists('curl_init')) {
            throw new ExtensionNotFoundException("Curl Client Extension Not Found", 1);
        }
        $this->client = curl_init();
    }

    /**
     * Return the all options for current curl ressource
     *
     * @see http://php.net/curl_getinfo
     * @return array
     */
    public function getOpts()
    {
        return curl_getinfo($this->client);
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
        curl_setopt($this->client, $option, $value);

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

        if(isset($this->proxyProtocols[$type])) {
            $this->setOpt(CURLOPT_PROXYTYPE, $this->proxyProtocols[$type]);
            // $this->setOpt(CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
        } else {
            throw new ProxyTypeErrorException("Unknown Proxy Type: {$type}", 1);
        }

        if($port) {
            $this->setOpt(CURLOPT_PROXYPORT, $port);
        }

        if($auth != '') {
            $this->setOpt(CURLOPT_PROXYAUTH, CURLAUTH_BASIC);
            $this->setOpt(CURLOPT_USERPWD, $auth);
        }
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
     * return Request Body
     *
     * @return bool|string
     * @author imxieke <oss@live.hk>
     * @copyright (c) 2024 CloudFlying
     */
    public function body()
    {
        return $this->body;
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
        if(! empty($this->info[$key])) {
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
     * Build Http Request
     *
     * @param string $method HTTP Request Method
     * @return void
     */
    public function build(string $method): void
    {
        if($method == 'GET') {
            $this->setOpt(CURLOPT_HTTPGET, true);
            if(!empty($this->options['query'])) {
                $this->url .= '?' . http_build_query($this->options['query']);
                $this->setOpt(CURLOPT_URL, $this->url);
            }
        } elseif($method == 'POST') {
            $this->setOpt(CURLOPT_POST, true);
            $this->setOpt(CURLOPT_POSTFIELDS, $this->options['json'] ? json_encode($this->options['json']) : $this->options['body']);
        } elseif(in_array($method, ['PUT', 'DELETE', 'OPTIONS'])) {
            // $this->setOpt(CURLOPT_POST, true);
            $this->setOpt(CURLOPT_CUSTOMREQUEST, $method);
            $this->setOpt(CURLOPT_POSTFIELDS, $this->fields);
        } elseif($method == 'HEAD') {
            // head nobody request need
            $this->setOpt(CURLOPT_NOBODY, true);
            $this->setOpt(CURLOPT_CUSTOMREQUEST, $method);
        } else {
            $this->setOpt(CURLOPT_CUSTOMREQUEST, $method);
        }

        $this->setOpt(CURLOPT_URL, $this->url);
        // Disable Direct Output Body To Console
        $this->setOpt(CURLOPT_RETURNTRANSFER, true);
        $this->setOpt(CURLOPT_USERAGENT, $this->options['headers']['User-Agent']);
        $this->setOpt(CURLOPT_HEADER, $this->showHeaderInfo);
        $this->setOpt(CURLOPT_TIMEOUT, $this->options['timeout']);
        $this->setOpt(CURLOPT_CONNECTTIMEOUT, $this->options['timeout']);
        $this->setOpt(CURLOPT_SSL_VERIFYPEER, $this->verifySsl);
        $this->setOpt(CURLOPT_FOLLOWLOCATION, $this->followLocation);
        $this->setOpt(CURLOPT_HTTPHEADER, $this->options['headers']);
        $this->setOpt(CURLOPT_VERBOSE, $this->debug);
        if(! empty($this->options['cookie_file'])) {
            $this->setOpt(CURLOPT_COOKIEFILE, $this->options['cookie_file']);
            $this->setOpt(CURLOPT_COOKIEJAR, $this->options['cookie_file']);
        }
        if(! empty($this->options['cookie'])) {
            $this->setOpt(CURLOPT_COOKIE, $this->options['cookie']);
        }
        if(! empty($this->options['headers']['Referer'])) {
            $this->setOpt(CURLOPT_REFERER, $this->options['headers']['Referer']);
        }
    }

    /**
     * 发起请求
     *
     * #TODO CURLOPT_ENCODING
     *
     * @param string $method Request Method
     * @param string|null $uri    Request Url
     * @param array $options Request Options
     * @return $this
     * @author imxieke <oss@live.hk>
     * @copyright (c) 2024 CloudFlying
     */
    public function request(string $method = 'GET', ?string $uri = null, array $options = [])
    {
        if (empty($this->options['base_uri']) && empty($uri)) {
            throw new InvalidParamException("Uri Can't be null");
        }

        if (!empty($uri)) {
            $this->url = strpos($uri, "://") ? $uri : $this->options['base_uri'] . $uri;
        }else {
            $this->url = $this->options['base_uri'];
        }

        $this->build($method);

        $this->body   = curl_exec($this->client);
        if ($this->body === false) {
            $this->errno = curl_errno($this->client);
            $this->error = curl_error($this->client);
        }

        $this->info  = curl_getinfo($this->client);
        $this->httpCode = $this->info['http_code'] ?? 200;

        // dump(curl_getinfo($this->client, CURLINFO_HEADER_SIZE));

        $this->close();
        return $this;
    }

    /**
     * Closing the current open curl resource.
     * @return void
     */
    public function close()
    {
        curl_close($this->client);
    }

    /**
     * Curl Supported Protocols
     *
     * @author imxieke <oss@live.hk>
     * @date 2025/10/18 13:10:12
     */
    public function getProtocols(): array
    {
        return curl_version()['protocols'] ?? [];
    }

    /**
     * Curl Client Version
     *
     * @return string
     */
    public function version(): string
    {
        return curl_version()['version'] ?? '';
    }

    /**
     * toString
     *
     * @return string
     * @author imxieke <oss@live.hk>
     * @date 2025/10/18 15:41:51
     */
    public function __toString()
    {
        return $this->body;
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
        $method = strtoupper(trim($method));
        if(!in_array($method, $this->methods)) {
            throw new MethodNotFoundException("Unknow Method $method", 1);
        }
        return $this->request($method, ...$args);
    }
}
