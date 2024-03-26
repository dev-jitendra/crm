<?php


namespace chillerlan\SettingsTest;

use chillerlan\Settings\SettingsContainerAbstract;


class TestContainer extends SettingsContainerAbstract{
	use TestOptionsTrait;

	private string $test3 = 'what';
}
