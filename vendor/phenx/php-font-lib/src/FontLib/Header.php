<?php

namespace FontLib;

use FontLib\TrueType\File;


abstract class Header extends BinaryStream {
  
  protected $font;
  protected $def = array();

  public $data;

  public function __construct(File $font) {
    $this->font = $font;
  }

  public function encode() {
    return $this->font->pack($this->def, $this->data);
  }

  public function parse() {
    $this->data = $this->font->unpack($this->def);
  }
}