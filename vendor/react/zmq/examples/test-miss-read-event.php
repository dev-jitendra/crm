<?php























require __DIR__.'/../vendor/autoload.php';

function pull_routine()
{
    $loop = React\EventLoop\Factory::create();

    $context = new React\ZMQ\Context($loop);
    $socket = $context->getSocket(ZMQ::SOCKET_PULL);
    $socket->bind('ipc:
    $socket->on('message', function() {
        echo "-";
    });

    $loop->run();
}

function push_routine()
{
    $zmq = new ZMQContext(1);
    $socket = $zmq->getSocket(ZMQ::SOCKET_PUSH, 'xyz');
    $socket->connect('ipc:

    while (true) {
        $msgs = rand(1, 300);
        for ($n = 0; $n < $msgs; $n++) {
            echo "+";
            $socket->send(json_encode('bogus-'.$n));
        }

        usleep(rand(0, 1000000));
    }
}

$pid = pcntl_fork();
if ($pid == 0) {
    pull_routine();
    exit;
}

push_routine();
