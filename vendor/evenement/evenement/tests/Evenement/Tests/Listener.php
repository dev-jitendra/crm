<?php declare(strict_types=1);



namespace Evenement\Tests;

class Listener
{
    private $data = [];

    private $magicData = [];

    private static $staticData = [];

    public function onFoo($data)
    {
        $this->data[] = $data;
    }

    public function __invoke($data)
    {
        $this->magicData[] = $data;
    }

    public static function onBar($data)
    {
        self::$staticData[] = $data;
    }

    public function getData()
    {
        return $this->data;
    }

    public function getMagicData()
    {
        return $this->magicData;
    }

    public static function getStaticData()
    {
        return self::$staticData;
    }
}
