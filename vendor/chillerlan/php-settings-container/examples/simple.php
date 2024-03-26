<?php


namespace chillerlan\SettingsExamples;

use chillerlan\Settings\SettingsContainerAbstract;

require_once __DIR__.'/../vendor/autoload.php';

class MyContainer extends SettingsContainerAbstract{
	protected $foo;
	protected $bar;
}


$container = new MyContainer(['foo' => 'what']);
$container->bar = 'foo';

var_dump($container->toJSON()); 


$container->nope = 'what';

var_dump($container->nope); 
