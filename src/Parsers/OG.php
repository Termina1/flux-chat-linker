<?php
namespace LinkParser\Parsers;

use LinkParser\Embed;
use React\Stream\ThroughStream;

class OG extends ThroughStream {

  protected function quote_trim($str) {
    return trim($str, "'\" \/");
  }

  public function filter($input) {
    $data = $input['data'];
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
    $result = array_replace_recursive(array('og:title' => '',
      'og:description' => '', 'og:image' => ''), $result);

    $embed = new Embed(array(
      'title' => $result['og:title'],
      'description' => $result['og:description'],
      'image' => $result['og:image']
    ));
    $input['source'] = $input['source']->merge($embed);
    return $input;
  }

}
