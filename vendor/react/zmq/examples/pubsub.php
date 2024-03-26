<?php

require __DIR__.'/../vendor/autoload.php';

$loop = React\EventLoop\Factory::create();

$context = new React\ZMQ\Context($loop);

$sub = $context->getSocket(ZMQ::SOCKET_SUB);
$sub->bind('tcp:
$sub->subscribe('foo');

$sub->on('message', function ($msg) {
    echo "Received: $msg\n";
});

$pub = $context->getSocket(ZMQ::SOCKET_PUB);
$pub->connect('tcp:

$i = 0;
$loop->addPeriodicTimer(1, function () use (&$i, $pub) {
    $i++;
    echo "publishing $i\n";
    $pub->send('foo '.$i);
});

$loop->run();
