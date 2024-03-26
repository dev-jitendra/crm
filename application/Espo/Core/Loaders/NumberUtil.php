<?php


namespace Espo\Core\Loaders;

use Espo\Core\Container\Loader;
use Espo\Core\Utils\Config;
use Espo\Core\Utils\NumberUtil as NumberUtilService;

class NumberUtil implements Loader
{
    public function __construct(private Config $config)
    {}

    public function load(): NumberUtilService
    {
        return new NumberUtilService($this->config->get('decimalMark'), $this->config->get('thousandSeparator'));
    }
}
