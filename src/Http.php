<?php
declare (strict_types = 1);

namespace Zeus;

use Zeus\Facade;
use Zeus\Http\Client;

/**
 * Http Client
 *
 * @method static Client get(string|UriInterface $uri, array $options = []) Create and send an HTTP GET request.
 * @method static Client head(string|UriInterface $uri, array $options = []) Create and send an HTTP GET request.
 * @method static Client put(string|UriInterface $uri, array $options = []) Create and send an HTTP GET request.
 * @method static Client delete(string|UriInterface $uri, array $options = []) Create and send an HTTP GET request.
 * @method static Client patch(string|UriInterface $uri, array $options = []) Create and send an HTTP GET request.
 * @method static Client connect(string|UriInterface $uri, array $options = []) Create and send an HTTP GET request.
 * @method static Client options(string|UriInterface $uri, array $options = []) Create and send an HTTP GET request.
 * @method static Client post(string|UriInterface $uri, array $options = []) Create and send an HTTP POST request.
 * @method static Client withJson(array $data = []) Set Http Request Json Body
 * @method static Client withQuery(array $data = []) Set Http GET Request Query
 * @method static Client withForm(array $data = []) Set Http Post Request Form Params
 * @method static Client withMultipart(array $data = []) Sets the body of the request to a multipart/form-data form.
 * @method static Client setBaseUri(string $uri) Base Uri
 * @method static Client withBearerToken(string $token) Bearer Authorization
 * @method static Client withUserAgent(string $userAgent) Http Request userAgent
 * @method static Client withHeader(string $key, string|int|float $value) Set Http Request Header
 * @method static Client withHeaders(array $headers) Mulltiple Set Http Header
 * @method static StreamInterface getBody() Query Response Body
 * @method static string getContents() Query Response Contents
 * @method static int getStatusCode() Query Response Status Code
 * @method static string|array|null getHeader() Query Response Header
 *
 * @author imxieke <oss@live.hk>
 * @copyright (c) 2025 CloudFlying
 * @date 2025/10/30 09:44:51
 */
class Http extends Facade
{
    public static function bind()
    {
        return Client::class;
    }
}
