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

function r404($response) {
  $response->writeHead(404);
  $response->end('');
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
    && (isset($request->getQuery()['token'])
    || isset($request->getQuery()['server']))) {
      cors($response);
      if(isset($request->getQuery()['server'])) {
        $poller->poll($request->getQuery()['server'])->pipe($response);
      } else if(isset($request->getQuery()['token'])) {
        $poller->requestServer($request->getQuery()['token'])->pipe($response);
      } else {
        r404($response);
      }
  } else {
    r404($response);
  }
});

$socket->listen(3000);
$loop->run();
