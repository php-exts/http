<?php
declare(strict_types=1);

namespace Zeus\Http;

use Zeus\Http\Driver\Curl;
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

    public function __construct()
    {
        // $this->ch = curl_init();
    }

    public function method(string $method)
    {
        $this->query([
            'url'
        ]);
    }

    public function __call(string $name, array $args)
    {
        $method = strtoupper(trim($name));
        switch ($method) {
            case 'GET':
                break;
            case 'POST':
                break;
            case 'HEAD':
                break;
            default:
                $this->method('GET');
        }
    }
}
