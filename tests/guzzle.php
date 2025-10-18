<?php
declare (strict_types = 1);

require_once dirname(__DIR__) . '/vendor/autoload.php';

use Zeus\Http\Client;
use Zeus\Http\Adapter\Guzzle;

$client = new Guzzle;

$response = $client->get('https://api.coze.cn/v1/bot/get_online_info', ['app' => ['query' => ['token' => '123456']]]);


dump($response);
