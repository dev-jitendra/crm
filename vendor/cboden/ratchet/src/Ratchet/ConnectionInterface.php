<?php
namespace Ratchet;


const VERSION = 'Ratchet/0.4.4';


interface ConnectionInterface {
    
    function send($data);

    
    function close();
}
