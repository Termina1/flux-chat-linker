<?php

namespace LinkParser\Parsers;

use LinkParser\Embed;
use React\Stream\ThroughStream;

class Dropbox extends ThroughStream {

  public function filter($input) {
    $path = parse_url($input['source']->link)['path'];
    if(!preg_match("/.*?\.(png|jpg|jpeg|gif|bmp)$/", $path)) {
      return $input;
    }
    $input['source'] = $input['source']->merge(new Embed(array(
      'image' => $input['source']->link . "&raw=1"
    )));
    return $input;
  }

}
