<?php



$ENTITIES_URL = 'http:

$payload = file_get_contents($ENTITIES_URL);
$json = json_decode($payload);

$table = array();
foreach ($json as $name => $obj) {
    $sname = substr($name, 1, -1);
    $table[$sname] = $obj->characters;
}

echo '<?php
namespace Masterminds\\HTML5;

class Entities {
  public static $byName = ';
var_export($table);
echo ';
}' . PHP_EOL;

