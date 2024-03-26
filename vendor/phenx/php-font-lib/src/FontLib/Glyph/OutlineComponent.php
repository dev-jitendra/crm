<?php


namespace FontLib\Glyph;

class OutlineComponent {
  public $flags;
  public $glyphIndex;
  public $a, $b, $c, $d, $e, $f;
  public $point_compound;
  public $point_component;
  public $instructions;

  function getMatrix() {
    return array(
      $this->a, $this->b,
      $this->c, $this->d,
      $this->e, $this->f,
    );
  }
}