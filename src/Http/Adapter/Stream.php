<?php
declare(strict_types=1);

namespace Zeus\Http\Adapter;

use Zeus\Exception\ClassNotFoundException;

/**
 * Stream Http Client Driver
 *
 * @author imxieke <oss@live.hk>
 * @copyright (c) 2024 CloudFlying
 */
class Stream
{
    protected $context;

    protected array $options;
    protected array $params;

    protected string $method = 'GET';

    protected float $protocolVersion = 1.1;
    protected float $timeout = 10.0;

    public function __construct()
    {
        $this->context = stream_context_create();
        if (!function_exists('stream_context_create')) {
            throw new ClassNotFoundException("Stream Class Not Found.");
        }
    }

    protected function setOptions(){
        $options = [
            'http' => [
                'method' => strtoupper($this->method),
                'protocol_version' => $this->protocolVersion,
                'header' => [
                    'Content-type: application/x-www-form-urlencoded',
                    'Accept: application/json',
                    'User-Agent: Zeus/1.0',
                    'Cookie: PHPSESSID=1',
                ],
                'timeout' => $this->timeout,
                'max_redirects' => 3,
                'content' => '',
                'proxy' => 'tcp://127.0.0.1:7890',
                'follow_location' => false,
                'ignore_errors' => false,
                'user_agent' => 'Zeus/1.0',
            ],
        ];
    }

    public function set()
    {

    }

    public function send(string $url)
    {
        return file_get_contents($url, false, $this->context);
    }
}
