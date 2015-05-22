<?php

namespace LinkParser;
use React\Stream\ThroughStream;
use React\Partial;
use LinkParser\Parsers\OG;
use LinkParser\Parsers\Dropbox;
use LinkParser\Request;
use LinkParser\Encoder;
use LinkParser\Embed;

function promiseAsStream($p) {
  $stream = new ThroughStream();
  $p->then(function($data) use ($stream) {
    $stream->end($data);
  });
  return $stream;
}

class ParserFactory {
  public function parse($link) {
    $type = $this->getLinkType($link);
    $req = $this->request($link);
    return $this->buildParser($type, $req);
  }

  protected function request($link) {
    $req = new Request();
    return promiseAsStream($req->get($link)->then(function($res) use ($link) {
      list($data, $response) = $res;
      return array('data' => $data,
        'response' => $response,
        'source' => new Embed(array('link' => $link)));
    }));
  }

  protected function getLinkType($link) {
    $parsed = parse_url($link);
    $host = str_replace('www.', '', $parsed['host']);
    return $host;
  }

  protected function buildParser($type, $req) {
    $stream = "";
    switch ($type) {
      case 'dropbox.com':
        $stream = $req->pipe(new OG())
          ->pipe(new Dropbox());
        break;

      default:
        $stream = $req->pipe(new OG());
        break;
    }

    return $stream->pipe(new Encoder());
  }
}
