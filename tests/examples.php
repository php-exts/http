<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/vendor/autoload.php';

use Zeus\Http\Client;

$http = new Client([
    'base_uri' => 'http://127.0.0.1:8080'
]);

$response = $http->withForm([
        'name' => 'Zeus',
        'age' => 20,
    ])
    ->withBody(["name" => "imxieke"])
    ->withHeader("Niubi", 666)
    ->withHeader('User-Agent', "Niubi")
    ->withUserAgent("Swoole Http")
    // ->withAuthByBasic('admin', '123456')
    // ->withAuthByDigest('admin', '123456')
    // ->withAuthByNtlm('admin', '123456')
    // ->withBearerToken("123456")
    ->withJson([
        'os' => "macOS"
    ])
    ->withCookies([
        'username' => "Niubi1912",
        'session_id' => "123456",
        'language' => "en-US",
        'theme' => "dark",
    ])
    ->withCookie('theme2', 'dark')
    // ->withProtocolVersion('1.0')
    ->withProtocolVersion('2_tls')
    ->withHeaderInfo()
    ->withDebug()
    ->post('/post');

dump($response->getBody()->getSize());
// echo $response->getContents();

// echo $response->getBody()->getContents();
// dump($http);
