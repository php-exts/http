<?php
declare (strict_types = 1);

namespace Zeus\Contract;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\RequestInterface;

interface HttpAdapterInterface
{
    public function getBody(): ResponseInterface;

    public function getContents(): string;
}
