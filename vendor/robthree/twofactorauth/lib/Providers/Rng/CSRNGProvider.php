<?php

namespace RobThree\Auth\Providers\Rng;

class CSRNGProvider implements IRNGProvider
{
    public function getRandomBytes($bytecount) {
        return random_bytes($bytecount);    
    }
    
    public function isCryptographicallySecure() {
        return true;
    }
}