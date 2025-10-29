<?php
declare(strict_types=1);

namespace Zeus\Http;

use Zeus\Http\Adapter\Curl;
use Zeus\Http\Adapter\Guzzle;
use Zeus\Http\Adapter\Stream;
use Zeus\Exception\ClassNotFoundException;
use Zeus\Exception\MethodNotFoundException;

/**
 * Http Client
 *
 * @author imxieke <oss@live.hk>
 * @copyright (c) 2025 CloudFlying
 * @date 2025/3/18 09:36:39
 */
class Client
{
    protected $driver;

    public function __construct($driver = Guzzle::class, array $options = [])
    {
        if(! class_exists($driver)) {
            throw new ClassNotFoundException("{$driver} Driver class not found");
        }
        $this->driver = new $driver($options);

    }

    public function __call(string $name, array $args)
    {
        return $this->driver->{$name}(...$args);
    }
}
