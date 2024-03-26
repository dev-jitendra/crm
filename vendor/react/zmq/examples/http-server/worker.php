<?php

$context = new ZMQContext();
$rep = $context->getSocket(ZMQ::SOCKET_REP);
$rep->connect('tcp:

while (true) {
    $msg = $rep->recv();
    var_dump($msg);
    $rep->send("LULZ\n");
}
