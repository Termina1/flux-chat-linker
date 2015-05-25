<?php

namespace LinkParser;
require_once(__DIR__ . '/util.php');

use LinkParser\Request;

class Poller {

  public function poll($server) {
    $req = new Request();
    return promiseAsStream($req->get($server)->then(function($res) {
      list($data, $response) = $res;
      return $data;
    }));
  }

}
