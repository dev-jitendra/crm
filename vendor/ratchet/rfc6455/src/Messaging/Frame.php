<?php
namespace Ratchet\RFC6455\Messaging;

class Frame implements FrameInterface {
    const OP_CONTINUE =  0;
    const OP_TEXT     =  1;
    const OP_BINARY   =  2;
    const OP_CLOSE    =  8;
    const OP_PING     =  9;
    const OP_PONG     = 10;

    const CLOSE_NORMAL      = 1000;
    const CLOSE_GOING_AWAY  = 1001;
    const CLOSE_PROTOCOL    = 1002;
    const CLOSE_BAD_DATA    = 1003;
    const CLOSE_NO_STATUS   = 1005;
    const CLOSE_ABNORMAL    = 1006;
    const CLOSE_BAD_PAYLOAD = 1007;
    const CLOSE_POLICY      = 1008;
    const CLOSE_TOO_BIG     = 1009;
    const CLOSE_MAND_EXT    = 1010;
    const CLOSE_SRV_ERR     = 1011;
    const CLOSE_TLS         = 1015;

    const MASK_LENGTH = 4;

    
    protected $data = '';

    
    public $bytesRecvd = 0;

    
    protected $defPayLen = -1;

    
    private $isCoalesced = false;

    
    protected $firstByte = -1;

    
    protected $secondByte = -1;

    
    private $ufeg;

    
    public function __construct($payload = null, $final = true, $opcode = 1, callable $ufExceptionFactory = null) {
        $this->ufeg = $ufExceptionFactory ?: static function($msg = '') {
            return new \UnderflowException($msg);
        };

        if (null === $payload) {
            return;
        }

        $this->defPayLen   = strlen($payload);
        $this->firstByte   = ($final ? 128 : 0) + $opcode;
        $this->secondByte  = $this->defPayLen;
        $this->isCoalesced = true;

        $ext = '';
        if ($this->defPayLen > 65535) {
            $ext = pack('NN', 0, $this->defPayLen);
            $this->secondByte = 127;
        } elseif ($this->defPayLen > 125) {
            $ext = pack('n', $this->defPayLen);
            $this->secondByte = 126;
        }

        $this->data       = chr($this->firstByte) . chr($this->secondByte) . $ext . $payload;
        $this->bytesRecvd = 2 + strlen($ext) + $this->defPayLen;
    }

    
    public function isCoalesced() {
        if (true === $this->isCoalesced) {
            return true;
        }

        try {
            $payload_length = $this->getPayloadLength();
            $payload_start  = $this->getPayloadStartingByte();
        } catch (\UnderflowException $e) {
            return false;
        }

        $this->isCoalesced = $this->bytesRecvd >= $payload_length + $payload_start;

        return $this->isCoalesced;
    }

    
    public function addBuffer($buf) {
        $len = strlen($buf);

        $this->data       .= $buf;
        $this->bytesRecvd += $len;

        if ($this->firstByte === -1 && $this->bytesRecvd !== 0) {
            $this->firstByte = ord($this->data[0]);
        }

        if ($this->secondByte === -1 && $this->bytesRecvd >= 2) {
            $this->secondByte = ord($this->data[1]);
        }
    }

    
    public function isFinal() {
        if (-1 === $this->firstByte) {
            throw call_user_func($this->ufeg, 'Not enough bytes received to determine if this is the final frame in message');
        }

        return 128 === ($this->firstByte & 128);
    }

    public function setRsv1($value = true) {
        if (strlen($this->data) == 0) {
            throw new \UnderflowException("Cannot set Rsv1 because there is no data.");
        }

        $this->firstByte =
            ($this->isFinal() ? 128 : 0)
            + $this->getOpcode()
            + ($value ? 64 : 0)
            + ($this->getRsv2() ? 32 : 0)
            + ($this->getRsv3() ? 16 : 0)
        ;

        $this->data[0] = chr($this->firstByte);
        return $this;
    }

    
    public function getRsv1() {
        if (-1 === $this->firstByte) {
            throw call_user_func($this->ufeg, 'Not enough bytes received to determine reserved bit');
        }

        return 64 === ($this->firstByte & 64);
    }

    
    public function getRsv2() {
        if (-1 === $this->firstByte) {
            throw call_user_func($this->ufeg, 'Not enough bytes received to determine reserved bit');
        }

        return 32 === ($this->firstByte & 32);
    }

    
    public function getRsv3() {
        if (-1 === $this->firstByte) {
            throw call_user_func($this->ufeg, 'Not enough bytes received to determine reserved bit');
        }

        return 16 === ($this->firstByte & 16);
    }

    
    public function isMasked() {
        if (-1 === $this->secondByte) {
            throw call_user_func($this->ufeg, "Not enough bytes received ({$this->bytesRecvd}) to determine if mask is set");
        }

        return 128 === ($this->secondByte & 128);
    }

    
    public function getMaskingKey() {
        if (!$this->isMasked()) {
            return '';
        }

        $start  = 1 + $this->getNumPayloadBytes();

        if ($this->bytesRecvd < $start + static::MASK_LENGTH) {
            throw call_user_func($this->ufeg, 'Not enough data buffered to calculate the masking key');
        }

        return substr($this->data, $start, static::MASK_LENGTH);
    }

    
    public function generateMaskingKey() {
        $mask = '';

        for ($i = 1; $i <= static::MASK_LENGTH; $i++) {
            $mask .= chr(rand(32, 126));
        }

        return $mask;
    }

    
    public function maskPayload($maskingKey = null) {
        if (null === $maskingKey) {
            $maskingKey = $this->generateMaskingKey();
        }

        if (static::MASK_LENGTH !== strlen($maskingKey)) {
            throw new \InvalidArgumentException("Masking key must be " . static::MASK_LENGTH ." characters");
        }

        if (extension_loaded('mbstring') && true !== mb_check_encoding($maskingKey, 'US-ASCII')) {
            throw new \OutOfBoundsException("Masking key MUST be ASCII");
        }

        $this->unMaskPayload();

        $this->secondByte = $this->secondByte | 128;
        $this->data[1]    = chr($this->secondByte);

        $this->data = substr_replace($this->data, $maskingKey, $this->getNumPayloadBytes() + 1, 0);

        $this->bytesRecvd += static::MASK_LENGTH;
        $this->data        = substr_replace($this->data, $this->applyMask($maskingKey), $this->getPayloadStartingByte(), $this->getPayloadLength());

        return $this;
    }

    
    public function unMaskPayload() {
        if (!$this->isCoalesced()) {
            throw call_user_func($this->ufeg, 'Frame must be coalesced before applying mask');
        }

        if (!$this->isMasked()) {
            return $this;
        }

        $maskingKey = $this->getMaskingKey();

        $this->secondByte = $this->secondByte & ~128;
        $this->data[1] = chr($this->secondByte);

        $this->data = substr_replace($this->data, '', $this->getNumPayloadBytes() + 1, static::MASK_LENGTH);

        $this->bytesRecvd -= static::MASK_LENGTH;
        $this->data        = substr_replace($this->data, $this->applyMask($maskingKey), $this->getPayloadStartingByte(), $this->getPayloadLength());

        return $this;
    }

    
    public function applyMask($maskingKey, $payload = null) {
        if (null === $payload) {
            if (!$this->isCoalesced()) {
                throw call_user_func($this->ufeg, 'Frame must be coalesced to apply a mask');
            }

            $payload = substr($this->data, $this->getPayloadStartingByte(), $this->getPayloadLength());
        }

        $len = strlen($payload);

        if (0 === $len) {
            return '';
        }

        return $payload ^ str_pad('', $len, $maskingKey, STR_PAD_RIGHT);
    }

    
    public function getOpcode() {
        if (-1 === $this->firstByte) {
            throw call_user_func($this->ufeg, 'Not enough bytes received to determine opcode');
        }

        return ($this->firstByte & ~240);
    }

    
    protected function getFirstPayloadVal() {
        if (-1 === $this->secondByte) {
            throw call_user_func($this->ufeg, 'Not enough bytes received');
        }

        return $this->secondByte & 127;
    }

    
    protected function getNumPayloadBits() {
        if (-1 === $this->secondByte) {
            throw call_user_func($this->ufeg, 'Not enough bytes received');
        }

        
        
        $bits = 7;

        
        $check = $this->getFirstPayloadVal();

        
        if ($check >= 126) {
            $bits += 16;
        }

        
        
        if ($check === 127) {
            $bits += 48;
        }

        return $bits;
    }

    
    protected function getNumPayloadBytes() {
        return (1 + $this->getNumPayloadBits()) / 8;
    }

    
    public function getPayloadLength() {
        if ($this->defPayLen !== -1) {
            return $this->defPayLen;
        }

        $this->defPayLen = $this->getFirstPayloadVal();
        if ($this->defPayLen <= 125) {
            return $this->getPayloadLength();
        }

        $byte_length = $this->getNumPayloadBytes();
        if ($this->bytesRecvd < 1 + $byte_length) {
            $this->defPayLen = -1;
            throw call_user_func($this->ufeg, 'Not enough data buffered to determine payload length');
        }

        $len = 0;
        for ($i = 2; $i <= $byte_length; $i++) {
            $len <<= 8;
            $len  += ord($this->data[$i]);
        }

        $this->defPayLen = $len;

        return $this->getPayloadLength();
    }

    
    public function getPayloadStartingByte() {
        return 1 + $this->getNumPayloadBytes() + ($this->isMasked() ? static::MASK_LENGTH : 0);
    }

    
    public function getPayload() {
        if (!$this->isCoalesced()) {
            throw call_user_func($this->ufeg, 'Can not return partial message');
        }

        return $this->__toString();
    }

    
    public function getContents() {
        return substr($this->data, 0, $this->getPayloadStartingByte() + $this->getPayloadLength());
    }

    public function __toString() {
        $payload = (string)substr($this->data, $this->getPayloadStartingByte(), $this->getPayloadLength());

        if ($this->isMasked()) {
            $payload = $this->applyMask($this->getMaskingKey(), $payload);
        }

        return $payload;
    }

    
    public function extractOverflow() {
        if ($this->isCoalesced()) {
            $endPoint  = $this->getPayloadLength();
            $endPoint += $this->getPayloadStartingByte();

            if ($this->bytesRecvd > $endPoint) {
                $overflow   = substr($this->data, $endPoint);
                $this->data = substr($this->data, 0, $endPoint);

                return $overflow;
            }
        }

        return '';
    }
}
