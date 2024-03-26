<?php


namespace chillerlan\SettingsTest;

use function sha1;

trait TestOptionsTrait{

	protected string $test1 = 'foo';

	protected ?bool $test2 = null;

	protected string $testConstruct;

	protected ?string $test4 = null;

	protected ?string $test5 = null;

	protected ?string $test6 = null;

	protected function TestOptionsTrait():void{
		$this->testConstruct = 'success';
	}

	protected function set_test5($value):void{
		$this->test5 = $value.'_test5';
	}

	protected function get_test6():string{
		return $this->test6 === null
			? 'null'
			: sha1($this->test6);
	}
}
