<?php
namespace LinkParser;

use React\Stream\ThroughStream;

class Encoder extends ThroughStream {
  public function filter($input) {
    return $input['source']->toJSON();
  }
}
