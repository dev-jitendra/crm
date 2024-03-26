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
    $zmq = new ZMQContext(1);
    $socket = $zmq->getSocket(ZMQ::SOCKET_PUSH, 'xyz');
    $socket->connect('ipc:

    while (true) {
        $msgs = rand(1, 300);
        for ($n = 0; $n < $msgs; $n++) {
            if (rand(0,100) >= 50) {
                echo "s";
                $socket->send('bogus-'.$n);
            }
            else {
                echo "m";
                $socket->sendmulti(array("bogus$n-1", "bogus$n-2", "bogus$n-3"));
            }
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
