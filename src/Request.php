<?php

namespace LinkParser;
use React\Promise\Promise;
use React\Dns\Resolver\Factory;
use React\HttpClient\Factory as HTTPFactory;

class Request {

  public function __construct() {
    global $loop;
    $dnsResolverFactory = new Factory();
    $dnsResolver = $dnsResolverFactory->createCached('8.8.8.8', $loop);
    $factory = new HTTPFactory();
    $this->client = $factory->create($loop, $dnsResolver);
  }

  public function get($url) {
    return $this->make("GET", $url);
  }

  protected function make($method, $url) {
    return new Promise(function($resolve) use ($method, $url) {
      $request = $this->client->request($method, $url);
      $result = "";
      $request->on('response', function ($response) use ($resolve, &$result) {
	  $headers = $response->getHeaders();
	  if(isset($headers['Location'])) {
            return $this->get($headers['Location'])->then(function($data) use ($resolve) {
              $resolve($data);
            });
          }
          $response->on('data', function ($data, $response) use (&$result) {
            $result .= $data;
          });

          $response->on('end', function ($error) use (&$result, $resolve, $response) {
            $resolve(array($result, $response));
          });

      });
      $request->end();
    });
  }

}
