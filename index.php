<?php
require 'vendor/autoload.php';
use LinkParser\ParserFactory;

$factory = new ParserFactory();
global $loop;

$loop = React\EventLoop\Factory::create();
$socket = new React\Socket\Server($loop);

$http = new React\Http\Server($socket);
$http->on('request', function ($request, $response) use ($factory) {
  if($request->getPath() != '/'
    || !isset($request->getQuery()['link'])
    || !$request->getQuery()['link']
    || !filter_var($request->getQuery()['link'], FILTER_VALIDATE_URL)) {
      $response->writeHead(404);
      $response->end('');
  } else {
    $response->writeHead(200, array('Content-Type' => 'application/json'));
    $factory->parse($request->getQuery()['link'])->pipe($response);
  }
});

$socket->listen(3000);
$loop->run();
