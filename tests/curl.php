<?php
declare (strict_types = 1);

require_once dirname(__DIR__) . '/vendor/autoload.php';

use Zeus\Http\Client;
use Zeus\Http\Adapter\Curl;

$client = new Curl([
    // 'base_uri' => 'https://api.coze.cn/v1/bot/',
    'base_uri' => 'http://127.0.0.1:8788/api',
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
    ->withJson([
        'os' => "macOS"
    ])
    ->withDebug()
    ->post();
    // ->delete("http://127.0.0.1:8083/get_online_info");
    // ->head("http://127.0.0.1:8083/get_online_info");
    // ->options("http://127.0.0.1:8083/get_online_info");
// $client = $client->withTimeout(60);
// $client = $client->request('POST');

// $client = new Client();

dump($client->body);

// echo ((string) $client);
// dump($response);
// dump($client->getOpts());
// dump($client->version());
