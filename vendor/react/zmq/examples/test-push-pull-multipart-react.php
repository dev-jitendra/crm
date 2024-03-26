<?php














require __DIR__.'/../vendor/autoload.php';

function pull_routine()
{
    $loop = React\EventLoop\Factory::create();

    $context = new React\ZMQ\Context($loop);
    $socket = $context->getSocket(ZMQ::SOCKET_PULL);
    $socket->bind('ipc:
    $socket->on('message', function($msg) {
        if (is_array($msg))
            echo "M";
        else
            echo "S";
    });

    $loop->run();
}

function push_routine()
{
    $loop = React\EventLoop\Factory::create();

    $context = new React\ZMQ\Context($loop);
    $socket = $context->getSocket(ZMQ::SOCKET_PUSH);
    $socket->connect('ipc:

    $loop->addPeriodicTimer(1, function () use ($socket) {
        for ($n = 0; $n < rand(1, 30000); $n++) {
            if (rand(0,100) >= 50) {
                echo "s";
                $socket->send('bogus-'.$n);
            }
            else {
                echo "m";
                $socket->send(array("bogus$n-1", "bogus$n-2", "bogus$n-3"));
            }
        }
    });

    $loop->run();

}

$pid = pcntl_fork();
if ($pid == 0) {
    pull_routine();
    exit;
}

push_routine();
