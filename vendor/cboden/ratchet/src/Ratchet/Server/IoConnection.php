<?php
namespace Ratchet\Server;
use Ratchet\ConnectionInterface;
use React\Socket\ConnectionInterface as ReactConn;


class IoConnection implements ConnectionInterface {
    
    protected $conn;


    
    public function __construct(ReactConn $conn) {
        $this->conn = $conn;
    }

    
    public function send($data) {
        $this->conn->write($data);

        return $this;
    }

    
    public function close() {
        $this->conn->end();
    }
}
