<?php


namespace FontLib\WOFF;

use FontLib\Table\DirectoryEntry;


class TableDirectoryEntry extends DirectoryEntry {
  public $origLength;

  function __construct(File $font) {
    parent::__construct($font);
  }

  function parse() {
    parent::parse();

    $font             = $this->font;
    $this->offset     = $font->readUInt32();
    $this->length     = $font->readUInt32();
    $this->origLength = $font->readUInt32();
    $this->checksum   = $font->readUInt32();
  }
}
