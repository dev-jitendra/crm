<?php declare(strict_types=1);



ini_set('memory_limit', '512M');

const ITERATIONS = 100000;

use Evenement\EventEmitter;

require __DIR__.'/../vendor/autoload.php';

$emitter = new EventEmitter();

$listeners = [];
for ($i = 0; $i < ITERATIONS; $i++) {
    $listeners[] = function ($a, $b, $c) {};
}

$start = microtime(true);
foreach ($listeners as $listener) {
    $emitter->once('event', $listener);
}
$time = microtime(true) - $start;
echo 'Adding ', number_format(ITERATIONS), ' once listeners took: ', number_format($time, 2), 's', PHP_EOL;

$start = microtime(true);
foreach ($listeners as $listener) {
    $emitter->removeListener('event', $listener);
}
$time = microtime(true) - $start;
echo 'Removing ', number_format(ITERATIONS), ' once listeners took: ', number_format($time, 2), 's', PHP_EOL;
