<?php

namespace LinkParser\Parsers;

use LinkParser\Embed;
use React\Stream\ThroughStream;

class Youtube extends ThroughStream {

  public function filter($input) {
    $source = $input['source'];
    if($source->og['og:type'] == 'video') {
      $input['source'] = $source->merge(new Embed(array(
        'snippet' => "<iframe frameborder=\"0\" allowfullscreen width=\"440\" height=\"247\" src=\"" . $source->og['og:video:url'] . "\"</iframe>"
      )));
    }
    return $input;
  }

}
