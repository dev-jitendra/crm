<?php
namespace Ratchet\RFC6455\Messaging;

class Message implements \IteratorAggregate, MessageInterface {
    
    private $_frames;

    
    private $len;

    #[\ReturnTypeWillChange]
    public function __construct() {
        $this->_frames = new \SplDoublyLinkedList;
        $this->len = 0;
    }

    #[\ReturnTypeWillChange]
    public function getIterator() {
        return $this->_frames;
    }

    
    #[\ReturnTypeWillChange]
    public function count() {
        return count($this->_frames);
    }

    
    #[\ReturnTypeWillChange]
    public function isCoalesced() {
        if (count($this->_frames) == 0) {
            return false;
        }

        $last = $this->_frames->top();

        return ($last->isCoalesced() && $last->isFinal());
    }

    
    public function addFrame(FrameInterface $fragment) {
        $this->len += $fragment->getPayloadLength();
        $this->_frames->push($fragment);

        return $this;
    }

    
    public function getOpcode() {
        if (count($this->_frames) == 0) {
            throw new \UnderflowException('No frames have been added to this message');
        }

        return $this->_frames->bottom()->getOpcode();
    }

    
    public function getPayloadLength() {
        return $this->len;
    }

    
    public function getPayload() {
        if (!$this->isCoalesced()) {
            throw new \UnderflowException('Message has not been put back together yet');
        }

        return $this->__toString();
    }

    
    public function getContents() {
        if (!$this->isCoalesced()) {
            throw new \UnderflowException("Message has not been put back together yet");
        }

        $buffer = '';

        foreach ($this->_frames as $frame) {
            $buffer .= $frame->getContents();
        }

        return $buffer;
    }

    public function __toString() {
        $buffer = '';

        foreach ($this->_frames as $frame) {
            $buffer .= $frame->getPayload();
        }

        return $buffer;
    }

    
    public function isBinary() {
        if ($this->_frames->isEmpty()) {
            throw new \UnderflowException('Not enough data has been received to determine if message is binary');
        }

        return Frame::OP_BINARY === $this->_frames->bottom()->getOpcode();
    }

    
    public function getRsv1() {
        if ($this->_frames->isEmpty()) {
            return false;
            
        }

        return $this->_frames->bottom()->getRsv1();
    }
}
