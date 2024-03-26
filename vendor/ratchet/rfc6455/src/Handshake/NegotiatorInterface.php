<?php
namespace Ratchet\RFC6455\Handshake;
use Psr\Http\Message\RequestInterface;


interface NegotiatorInterface {
    const GUID = '258EAFA5-E914-47DA-95CA-C5AB0DC85B11';

    
    function isProtocol(RequestInterface $request);

    
    function getVersionNumber();

    
    function handshake(RequestInterface $request);

    
    function setSupportedSubProtocols(array $protocols);

    
    function setStrictSubProtocolCheck($enable);
}
