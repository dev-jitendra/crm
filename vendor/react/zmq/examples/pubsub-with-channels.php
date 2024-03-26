<?php

require __DIR__.'/../vendor/autoload.php';

$loop = React\EventLoop\Factory::create();

$context = new React\ZMQ\Context($loop);

$sub = $context->getSocket(\ZMQ::SOCKET_SUB);
$sub->connect('tcp:
$sub->subscribe('sub');
$sub->on('messages', function ($msg) {
    echo "Received: ". $msg[1] ." on channel: ". $msg[0] ."\n";
});

$bus = $context->getSocket(\ZMQ::SOCKET_SUB);
$bus->connect('tcp:
$bus->subscribe('bus');
$bus->on('messages', function ($msg) {
    echo $msg[0] ." :lennahc no ". $msg[1] ." :devieceR\n";
});

$pub = $context->getSocket(\ZMQ::SOCKET_PUB);
$pub->bind('tcp:
$i = 0;
$loop->addPeriodicTimer(1, function () use (&$i, $pub) {
    $i++;
    echo "publishing $i\n";
    $pub->sendmulti(array('sub', $i)); 
    $pub->sendmulti(array('bus', $i)); 
});

$loop->run();
