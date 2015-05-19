<?php

namespace LinkParser;
use React\Stream\ReadableStream;
use React\Partial;
use LinkParser\Parsers\OG;

class ParserFactory {
  public function parse($link) {
    $source = new ReadableStream();
    $parser = $this->buildParser($link);
    $parser->extract($link)->then(Partial\bind([$this, 'resolve'], $source));
    return $source;
  }

  public function resolve($source, $result) {
    $source->emit('data', [$result->toJSON()]);
    $source->close();
  }

  protected function buildParser($link) {
    return new OG();
  }
}
