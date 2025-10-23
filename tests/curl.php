<?php
declare (strict_types = 1);

require_once dirname(__DIR__) . '/vendor/autoload.php';

use Zeus\Http\Client;
use Zeus\Http\Adapter\Curl;

$client = new Curl([
    // 'base_uri' => 'https://api.coze.cn/v1/bot/',
    'base_uri' => 'http://127.0.0.1:8083',
    'query' => [
        'token' => '123456',
    ]
]);

// $client = $client->withHeaderInfo();

// $response = $client->request();
// $client = $client->request('GET', 'get_online_info');
// $client = $client->request('POST', 'get_online_info');
// $client =$client->get("/get_online_info");
$client =$client
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
    ->post();
    // ->delete("http://127.0.0.1:8083/get_online_info");
    // ->head("http://127.0.0.1:8083/get_online_info");
    // ->options("http://127.0.0.1:8083/get_online_info");
// $client = $client->withTimeout(60);
// $client = $client->request('POST');

// $client = new Client();

// dump($client->body);
// dump($client);
// dump($client->getHeader('x-powered-by'));

echo ((string) $client);
// dump($response);
// dump($client->getOpts());
// dump($client->version());
