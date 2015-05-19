<?php
namespace LinkParser\Parsers;

use React\Promise\Promise;
use React\SocketClient\Connector;
use React\EventLoop\Factory;
use LinkParser\Request;
use LinkParser\Embed;

class OG {

  public function extract($link) {
    $req = new Request();
    return $req->get($link)->then(function($data) {
      $og = $this->parse($data);
      return new Promise(function($r) use ($og) { $r($og); });
    });
  }

  protected function quote_trim($str) {
    return trim($str, "'\" ");
  }

  protected function parse($data) {
    $res = strpos($data, '</head>');
    if($res == false) {
      //TODO: handle error
    }
    $head = substr($data, 0, $res);
    $matches = array();
    $amt = preg_match_all("/<meta.*?(content=(.*?))?(property=(.*?))?(content=(.*?))?>/", $head, $matches);
    $props = array('og:title', 'og:description', 'og:image');
    $result = array();
    for($i = 0; $i < $amt; $i++) {
      $prop = $this->quote_trim($matches[4][$i]);
      if(in_array($prop, $props)) {
        $result[$prop] = $matches[2][$i] ?
          $this->quote_trim($matches[2][$i]) :
          $this->quote_trim($matches[6][$i]);
      }
    }
    return new Embed($result['og:title'], $result['og:description'], $result['og:image']);
  }

}
