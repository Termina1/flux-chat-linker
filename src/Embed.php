<?php

namespace LinkParser;

class Embed {

  protected $properties;

  public function __construct($properties) {
    $this->properties = $properties;
  }

  public function __get($name) {
    return $this->properties[$name];
  }

  public function getProperties() {
    return $this->properties;
  }

  public function merge(Embed $embed) {
    return new Embed(array_replace_recursive($this->properties,
      $embed->getProperties()));
  }

  public function toJSON() {
    return json_encode($this->properties);
  }

}
