<?php

namespace LinkParser;
require_once(__DIR__ . '/util.php');

use React\Partial;
use LinkParser\Parsers\OG;
use LinkParser\Parsers\Dropbox;
use LinkParser\Parsers\Youtube;
use LinkParser\Request;
use LinkParser\Encoder;
use LinkParser\Embed;
use React\Promise\Promise;

class ParserFactory {
  public function parse($link) {
    $type = $this->getLinkType($link);
    return $this->buildParser($type, $link);
  }

  protected function request($link, $type) {
    $req = new Request();
    return promiseAsStream($req->get($link)->then(function($res) use ($link, $type) {
      list($data, $response) = $res;
      return array('data' => $data,
        'response' => $response,
        'source' => new Embed(array('link' => $link, 'type' => $type)));
    }));
  }

  protected function simple($link, $type) {
    return promiseAsStream(new Promise(function($full) use ($type, $link) {
      global $loop;
      $loop->nextTick(function() use ($type, $link, $full) {
        $full(array(
          'source' => new Embed(array('link' => $link, 'type' => $type))
        ));
      });
    }));
  }

  protected function getLinkType($link) {
    $parsed = parse_url($link);
    $host = str_replace('www.', '', $parsed['host']);
    return $host;
  }

  protected function buildParser($type, $link) {
    $stream = "";
    switch ($type) {
      case 'dropbox.com':
        $stream = $this->simple($link, $type)->pipe(new Dropbox());
        break;

      case 'youtube.com':
        $stream = $this->request($link, $type)
          ->pipe(new OG())
          ->pipe(new Youtube());
        break;

      default:
        $stream = $this->request($link, $type)->pipe(new OG());
        break;
    }

    return $stream->pipe(new Encoder());
  }
}
