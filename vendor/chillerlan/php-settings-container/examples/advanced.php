<?php


namespace chillerlan\SettingsExamples;

use chillerlan\Settings\SettingsContainerAbstract;

require_once __DIR__.'/../vendor/autoload.php';


trait SomeOptions{
	protected string $foo = '';

	
	protected function SomeOptions(){
		
		$this->foo = strtoupper($this->foo);
	}
}


trait MoreOptions{
	protected string $bar = 'whatever'; 
}

$commonOptions = [
	
	'foo' => 'whatever',
	
	'bar' => 'nothing',
];




class MySettings extends SettingsContainerAbstract{
	use SomeOptions, MoreOptions; 
};

$container = new MySettings($commonOptions);

var_dump($container->foo); 
var_dump($container->bar); 
