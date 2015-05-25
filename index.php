<?php
require 'vendor/autoload.php';
use LinkParser\ParserFactory;
use LinkParser\Poller;

$factory = new ParserFactory();
$poller = new Poller();
global $loop;

$loop = React\EventLoop\Factory::create();
$socket = new React\Socket\Server($loop);

function cors($response) {
  $response->writeHead(200, array(
    'Content-Type' => 'application/json',
    'Access-Control-Allow-Origin' => '*',
    'Access-Control-Allow-Methods' => 'GET'
  ));
}

$http = new React\Http\Server($socket);
$http->on('request', function ($request, $response) use ($factory, $poller) {
  if($request->getPath() == '/'
    && isset($request->getQuery()['link'])
    && $request->getQuery()['link']
    && filter_var($request->getQuery()['link'], FILTER_VALIDATE_URL)) {
      cors($response);
      $factory->parse($request->getQuery()['link'])->pipe($response);
  } elseif($request->getPath() == '/poll'
    && isset($request->getQuery()['server'])
    && $request->getQuery()['server']) {
      cors($response);
      $poller->poll($request->getQuery()['server'])->pipe($response);
  } else {
    $response->writeHead(404);
    $response->end('');
  }
});

$socket->listen(3000);
$loop->run();
