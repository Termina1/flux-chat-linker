<?php
require 'vendor/autoload.php';
use LinkParser\ParserFactory;

$factory = new ParserFactory();
global $loop;

$loop = React\EventLoop\Factory::create();
$socket = new React\Socket\Server($loop);

$http = new React\Http\Server($socket);
$http->on('request', function ($request, $response) use ($factory) {
  $response->writeHead(200, array('Content-Type' => 'application/json'));
  $factory->parse($request->getQuery()['link'])->pipe($response);
});

$socket->listen(3000);
$loop->run();
