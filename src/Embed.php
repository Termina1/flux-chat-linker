<?php

namespace LinkParser;

class Embed {

  protected $title;
  protected $description;
  protected $image;

  public function __construct($title, $description, $image) {
    $this->description = $description;
    $this->title = $title;
    $this->image = $image;
  }

  public function toJSON() {
    return json_encode(array(
      'description' => $this->description,
      'image' => $this->image,
      'title' => $this->title
    ));
  }

}
