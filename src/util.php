<?php

use React\Stream\ThroughStream;

function promiseAsStream($p) {
  $stream = new ThroughStream();
  $p->then(function($data) use ($stream) {
    $stream->end($data);
  });
  return $stream;
}
