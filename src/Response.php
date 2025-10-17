<?php
declare(strict_types=1);

namespace Zeus\Http;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\RequestInterface;

/**
 * Response
 *
 * @author imxieke <oss@live.hk>
 * @copyright (c) 2025 CloudFlying
 * @date 2025/10/17 17:18:25
 */
class Response
{
    /**
     * 状态码
     */
    protected int $code;

    /**
     * Message
     *
     * @var string
     * @author CloudFlying
     * @date 2025/10/17 17:23:45
     */
    protected string $msg;

    /**
     * Response Headers
     *
     * @var array
     * @author CloudFlying
     * @date 2025/10/17 17:24:05
     */
    protected array $headers = [];
}
