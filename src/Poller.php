<?php

namespace LinkParser;
require_once(__DIR__ . '/util.php');

define("VK_API", "https://api.vk.com/method/");
define("VERSION", "5.33");

use LinkParser\Request;

class Poller {

  public function poll($server) {
    $req = new Request();
    return promiseAsStream($req->get($server)->then(function($res) {
      list($data, $response) = $res;
      return $data;
    }));
  }

  public function requestServer($token) {
    $req = new Request();
    $url = VK_API."messages.getLongPollServer?access_token=$token&v=".VERSION;
    return promiseAsStream($req->get($url)->then(function($res) {
      list($data, $response) = $res;
      return $data;
    }));
  }

}
