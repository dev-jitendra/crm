<?php

namespace Ratchet\RFC6455\Test\Unit\Messaging;

use Ratchet\RFC6455\Messaging\CloseFrameChecker;
use Ratchet\RFC6455\Messaging\Frame;
use Ratchet\RFC6455\Messaging\Message;
use Ratchet\RFC6455\Messaging\MessageBuffer;
use React\EventLoop\Factory;
use PHPUnit\Framework\TestCase;

class MessageBufferTest extends TestCase
{
    
    public function testProcessingLotsOfFramesInASingleChunk() {
        $frame = new Frame('a', true, Frame::OP_TEXT);

        $frameRaw = $frame->getContents();

        $data = str_repeat($frameRaw, 1000);

        $messageCount = 0;

        $messageBuffer = new MessageBuffer(
            new CloseFrameChecker(),
            function (Message $message) use (&$messageCount) {
                $messageCount++;
                $this->assertEquals('a', $message->getPayload());
            },
            null,
            false
        );

        $messageBuffer->onData($data);

        $this->assertEquals(1000, $messageCount);
    }

    public function testProcessingMessagesAsynchronouslyWhileBlockingInMessageHandler() {
        $loop = Factory::create();

        $frameA = new Frame('a', true, Frame::OP_TEXT);
        $frameB = new Frame('b', true, Frame::OP_TEXT);

        $bReceived = false;

        $messageBuffer = new MessageBuffer(
            new CloseFrameChecker(),
            function (Message $message) use (&$messageCount, &$bReceived, $loop) {
                $payload = $message->getPayload();
                $bReceived = $payload === 'b';

                if (!$bReceived) {
                    $loop->run();
                }
            },
            null,
            false
        );

        $loop->addPeriodicTimer(0.1, function () use ($messageBuffer, $frameB, $loop) {
            $loop->stop();
            $messageBuffer->onData($frameB->getContents());
        });

        $messageBuffer->onData($frameA->getContents());

        $this->assertTrue($bReceived);
    }

    public function testInvalidFrameLength() {
        $frame = new Frame(str_repeat('a', 200), true, Frame::OP_TEXT);

        $frameRaw = $frame->getContents();

        $frameRaw[1] = "\x7f"; 

        $frameRaw[2] = "\xff"; 
        $frameRaw[3] = "\xff";
        $frameRaw[4] = "\xff";
        $frameRaw[5] = "\xff";
        $frameRaw[6] = "\xff";
        $frameRaw[7] = "\xff";
        $frameRaw[8] = "\xff";
        $frameRaw[9] = "\xff";

        
        $controlFrame = null;
        $messageCount = 0;

        $messageBuffer = new MessageBuffer(
            new CloseFrameChecker(),
            function (Message $message) use (&$messageCount) {
                $messageCount++;
            },
            function (Frame $frame) use (&$controlFrame) {
                $this->assertNull($controlFrame);
                $controlFrame = $frame;
            },
            false,
            null,
            0,
            10
        );

        $messageBuffer->onData($frameRaw);

        $this->assertEquals(0, $messageCount);
        $this->assertTrue($controlFrame instanceof Frame);
        $this->assertEquals(Frame::OP_CLOSE, $controlFrame->getOpcode());
        $this->assertEquals([Frame::CLOSE_PROTOCOL], array_merge(unpack('n*', substr($controlFrame->getPayload(), 0, 2))));

    }

    public function testFrameLengthTooBig() {
        $frame = new Frame(str_repeat('a', 200), true, Frame::OP_TEXT);

        $frameRaw = $frame->getContents();

        $frameRaw[1] = "\x7f"; 

        $frameRaw[2] = "\x7f"; 
        $frameRaw[3] = "\xff";
        $frameRaw[4] = "\xff";
        $frameRaw[5] = "\xff";
        $frameRaw[6] = "\xff";
        $frameRaw[7] = "\xff";
        $frameRaw[8] = "\xff";
        $frameRaw[9] = "\xff";

        
        $controlFrame = null;
        $messageCount = 0;

        $messageBuffer = new MessageBuffer(
            new CloseFrameChecker(),
            function (Message $message) use (&$messageCount) {
                $messageCount++;
            },
            function (Frame $frame) use (&$controlFrame) {
                $this->assertNull($controlFrame);
                $controlFrame = $frame;
            },
            false,
            null,
            0,
            10
        );

        $messageBuffer->onData($frameRaw);

        $this->assertEquals(0, $messageCount);
        $this->assertTrue($controlFrame instanceof Frame);
        $this->assertEquals(Frame::OP_CLOSE, $controlFrame->getOpcode());
        $this->assertEquals([Frame::CLOSE_TOO_BIG], array_merge(unpack('n*', substr($controlFrame->getPayload(), 0, 2))));
    }

    public function testFrameLengthBiggerThanMaxMessagePayload() {
        $frame = new Frame(str_repeat('a', 200), true, Frame::OP_TEXT);

        $frameRaw = $frame->getContents();

        
        $controlFrame = null;
        $messageCount = 0;

        $messageBuffer = new MessageBuffer(
            new CloseFrameChecker(),
            function (Message $message) use (&$messageCount) {
                $messageCount++;
            },
            function (Frame $frame) use (&$controlFrame) {
                $this->assertNull($controlFrame);
                $controlFrame = $frame;
            },
            false,
            null,
            100,
            0
        );

        $messageBuffer->onData($frameRaw);

        $this->assertEquals(0, $messageCount);
        $this->assertTrue($controlFrame instanceof Frame);
        $this->assertEquals(Frame::OP_CLOSE, $controlFrame->getOpcode());
        $this->assertEquals([Frame::CLOSE_TOO_BIG], array_merge(unpack('n*', substr($controlFrame->getPayload(), 0, 2))));
    }

    public function testSecondFrameLengthPushesPastMaxMessagePayload() {
        $frame = new Frame(str_repeat('a', 200), false, Frame::OP_TEXT);
        $firstFrameRaw = $frame->getContents();
        $frame = new Frame(str_repeat('b', 200), true, Frame::OP_TEXT);
        $secondFrameRaw = $frame->getContents();

        
        $controlFrame = null;
        $messageCount = 0;

        $messageBuffer = new MessageBuffer(
            new CloseFrameChecker(),
            function (Message $message) use (&$messageCount) {
                $messageCount++;
            },
            function (Frame $frame) use (&$controlFrame) {
                $this->assertNull($controlFrame);
                $controlFrame = $frame;
            },
            false,
            null,
            300,
            0
        );

        $messageBuffer->onData($firstFrameRaw);
        
        $messageBuffer->onData(substr($secondFrameRaw, 0, 150));

        $this->assertEquals(0, $messageCount);
        $this->assertTrue($controlFrame instanceof Frame);
        $this->assertEquals(Frame::OP_CLOSE, $controlFrame->getOpcode());
        $this->assertEquals([Frame::CLOSE_TOO_BIG], array_merge(unpack('n*', substr($controlFrame->getPayload(), 0, 2))));
    }

    

    
    public function testMemoryLimits($phpConfigurationValue, $expectedLimit) {
        $method = new \ReflectionMethod('Ratchet\RFC6455\Messaging\MessageBuffer', 'getMemoryLimit');
        $method->setAccessible(true);
        $actualLimit = $method->invoke(null, $phpConfigurationValue);

        $this->assertSame($expectedLimit, $actualLimit);
    }

    public function phpConfigurationProvider() {
        return [
            'without unit type, just bytes' => ['500', 500],
            '1 GB with big "G"' => ['1G', 1 * 1024 * 1024 * 1024],
            '128 MB with big "M"' => ['128M', 128 * 1024 * 1024],
            '128 MB with small "m"' => ['128m', 128 * 1024 * 1024],
            '24 kB with small "k"' => ['24k', 24 * 1024],
            '2 GB with small "g"' => ['2g', 2 * 1024 * 1024 * 1024],
            'unlimited memory' => ['-1', 0],
            'invalid float value' => ['2.5M', 2 * 1024 * 1024],
            'empty value' => ['', 0],
            'invalid ini setting' => ['whatever it takes', 0]
        ];
    }

    
    public function testInvalidMaxFramePayloadSizes() {
        $messageBuffer = new MessageBuffer(
            new CloseFrameChecker(),
            function (Message $message) {},
            function (Frame $frame) {},
            false,
            null,
            0,
            0x8000000000000000
        );
    }

    
    public function testInvalidMaxMessagePayloadSizes() {
        $messageBuffer = new MessageBuffer(
            new CloseFrameChecker(),
            function (Message $message) {},
            function (Frame $frame) {},
            false,
            null,
            0x8000000000000000,
            0
        );
    }

    
    public function testIniSizes($phpConfigurationValue, $expectedLimit) {
        $value = @ini_set('memory_limit', $phpConfigurationValue);
        if ($value === false) {
           $this->markTestSkipped("Does not support setting the memory_limit lower than current memory_usage");
        }

        $messageBuffer = new MessageBuffer(
            new CloseFrameChecker(),
            function (Message $message) {},
            function (Frame $frame) {},
            false,
            null
        );

        if ($expectedLimit === -1) {
            $expectedLimit = 0;
        }

        $prop = new \ReflectionProperty($messageBuffer, 'maxMessagePayloadSize');
        $prop->setAccessible(true);
        $this->assertEquals($expectedLimit / 4, $prop->getValue($messageBuffer));

        $prop = new \ReflectionProperty($messageBuffer, 'maxFramePayloadSize');
        $prop->setAccessible(true);
        $this->assertEquals($expectedLimit / 4, $prop->getValue($messageBuffer));
    }

    
    public function testInvalidIniSize() {
        $value = @ini_set('memory_limit', 'lots of memory');
        if ($value === false) {
            $this->markTestSkipped("Does not support setting the memory_limit lower than current memory_usage");
        }

        $messageBuffer = new MessageBuffer(
            new CloseFrameChecker(),
            function (Message $message) {},
            function (Frame $frame) {},
            false,
            null
        );

        $prop = new \ReflectionProperty($messageBuffer, 'maxMessagePayloadSize');
        $prop->setAccessible(true);
        $this->assertEquals(0, $prop->getValue($messageBuffer));

        $prop = new \ReflectionProperty($messageBuffer, 'maxFramePayloadSize');
        $prop->setAccessible(true);
        $this->assertEquals(0, $prop->getValue($messageBuffer));
    }
}