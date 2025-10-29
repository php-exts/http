<?php
declare(strict_types=1);

namespace Zeus\Http;

use Zeus\Http\Adapter\Curl;
use Zeus\Http\Adapter\Guzzle;
use Zeus\Http\Adapter\Stream;

use Psr\Http\Message\ResponseInterface;

use GuzzleHttp\Psr7\Utils as Psr7Utils;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request as Psr7Request;
use GuzzleHttp\Psr7\Response as Psr7Response;

use GuzzleHttp\Event\{
    CompleteEvent,
    MessageCompleteEvent,
};

use Zeus\Exception\ClassNotFoundException;
use Zeus\Exception\MethodNotFoundException;

/**
 * Http Client
 *
 * @method Client get(string|UriInterface $uri, array $options = []) Create and send an HTTP GET request.
 * @method Client head(string|UriInterface $uri, array $options = []) Create and send an HTTP GET request.
 * @method Client put(string|UriInterface $uri, array $options = []) Create and send an HTTP GET request.
 * @method Client delete(string|UriInterface $uri, array $options = []) Create and send an HTTP GET request.
 * @method Client patch(string|UriInterface $uri, array $options = []) Create and send an HTTP GET request.
 * @method Client connect(string|UriInterface $uri, array $options = []) Create and send an HTTP GET request.
 * @method Client options(string|UriInterface $uri, array $options = []) Create and send an HTTP GET request.
 * @method Client post(string|UriInterface $uri, array $options = []) Create and send an HTTP POST request.
 *
 * @author imxieke <oss@live.hk>
 * @copyright (c) 2025 CloudFlying
 * @date 2025/3/18 09:36:39
 */
class Client extends Adapter
{
    protected $driver;

    public function __construct(array $options = [])
    {
        $this->options = array_merge($this->options, $options);
    }

    /**
     * Set Http Client
     *
     * @param mixed $driver
     * @throws ClassNotFoundException
     * @return static
     * @author imxieke <oss@live.hk>
     * @date 2025/10/29 21:15:31
     */
    public function setDriver($driver = Guzzle::class)
    {
        if(! class_exists($driver)) {
            throw new ClassNotFoundException("{$driver} Driver class not found");
        }
        $this->driver = new $driver($this->options);
        return $this;
    }

    /**
     * Http Request Method
     * @param mixed $method
     * @param mixed $args
     * @return Client
     * @throws MethodNotFoundException
     * @author imxieke <oss@live.hk>
     * @copyright (c) 2024 CloudFlying
     */
    public function __call($method, $args): Client
    {
        $this->setDriver();
        $method = strtoupper(trim($method));
        if(! in_array($method, $this->methods)) {
            throw new MethodNotFoundException("Unknow Http Method $method");
        }
        $this->response = $this->driver->request($method, ...$args);
        return $this;
    }
}
