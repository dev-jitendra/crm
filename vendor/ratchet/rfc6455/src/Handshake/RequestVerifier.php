<?php
namespace Ratchet\RFC6455\Handshake;
use Psr\Http\Message\RequestInterface;


class RequestVerifier {
    const VERSION = 13;

    
    public function verifyAll(RequestInterface $request) {
        $passes = 0;

        $passes += (int)$this->verifyMethod($request->getMethod());
        $passes += (int)$this->verifyHTTPVersion($request->getProtocolVersion());
        $passes += (int)$this->verifyRequestURI($request->getUri()->getPath());
        $passes += (int)$this->verifyHost($request->getHeader('Host'));
        $passes += (int)$this->verifyUpgradeRequest($request->getHeader('Upgrade'));
        $passes += (int)$this->verifyConnection($request->getHeader('Connection'));
        $passes += (int)$this->verifyKey($request->getHeader('Sec-WebSocket-Key'));
        $passes += (int)$this->verifyVersion($request->getHeader('Sec-WebSocket-Version'));

        return (8 === $passes);
    }

    
    public function verifyMethod($val) {
        return ('get' === strtolower($val));
    }

    
    public function verifyHTTPVersion($val) {
        return (1.1 <= (double)$val);
    }

    
    public function verifyRequestURI($val) {
        if ($val[0] !== '/') {
            return false;
        }

        if (false !== strstr($val, '#')) {
            return false;
        }

        if (!extension_loaded('mbstring')) {
            return true;
        }

        return mb_check_encoding($val, 'US-ASCII');
    }

    
    public function verifyHost(array $hostHeader) {
        return (1 === count($hostHeader));
    }

    
    public function verifyUpgradeRequest(array $upgradeHeader) {
        return (1 === count($upgradeHeader) && 'websocket' === strtolower($upgradeHeader[0]));
    }

    
    public function verifyConnection(array $connectionHeader) {
        foreach ($connectionHeader as $l) {
            $upgrades = array_filter(
                array_map('trim', array_map('strtolower', explode(',', $l))),
                function ($x) {
                    return 'upgrade' === $x;
                }
            );
            if (count($upgrades) > 0) {
                return true;
            }
        }
        return false;
    }

    
    public function verifyKey(array $keyHeader) {
        return (1 === count($keyHeader) && 16 === strlen(base64_decode($keyHeader[0])));
    }

    
    public function verifyVersion(array $versionHeader) {
        return (1 === count($versionHeader) && static::VERSION === (int)$versionHeader[0]);
    }

    
    public function verifyProtocol($val) {
    }

    
    public function verifyExtensions($val) {
    }

    public function getPermessageDeflateOptions(array $requestHeader, array $responseHeader) {
        $deflate = true;
        if (!isset($requestHeader['Sec-WebSocket-Extensions']) || count(array_filter($requestHeader['Sec-WebSocket-Extensions'], function ($val) {
            return 'permessage-deflate' === substr($val, 0, strlen('permessage-deflate'));
        })) === 0) {
             $deflate = false;
        }

        if (!isset($responseHeader['Sec-WebSocket-Extensions']) || count(array_filter($responseHeader['Sec-WebSocket-Extensions'], function ($val) {
                return 'permessage-deflate' === substr($val, 0, strlen('permessage-deflate'));
            })) === 0) {
            $deflate = false;
        }

        return [
            'deflate' => $deflate,
            'no_context_takeover' => false,
            'max_window_bits' => null,
            'request_no_context_takeover' => false,
            'request_max_window_bits' => null
        ];
    }
}
