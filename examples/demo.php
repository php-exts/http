<?php
declare (strict_types = 1);

require_once dirname(__DIR__) . "/vendor/autoload.php";

use Zeus\Http\Adapter\Guzzle;

$client = new Guzzle();

$url = 'https://api.coze.cn/v1/bot/get_online_info';

$response = $client->get($url, ['app' => ['query' => ['token' => '123456']]]);
// $response = $client->options($url, ['app' => ['query' => ['token' => '123456']]]);

// dump($client);
dump((string) $response);
// dump((string) $response->getBody());
// dump(
//     $response->getBody()->getContents(),
//     $response->getBody()->getMetadata(),
//     $response->getBody()->getSize()
// );
