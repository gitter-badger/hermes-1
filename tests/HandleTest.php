<?php

namespace Tomaj\Hermes;

use PHPUnit_Framework_TestCase;
use Tomaj\Hermes\Driver\DummyDriver;
use Tomaj\Hermes\Handler\TestHandler;
use Tomaj\Hermes\Handler\ExceptionHandler;

require __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/handler/TestHandler.php';
require_once __DIR__ . '/handler/ExceptionHandler.php';

class HandleTest extends PHPUnit_Framework_TestCase
{
    public function testEmitWithDummyDriver()
    {
        $message1 = new Message('event1', ['a' => 'b']);
        $message2 = new Message('event2', ['c' => 'd']);

        $driver = new DummyDriver([$message1, $message2]);
        $dispatcher = new Dispatcher($driver);

        $handler = new TestHandler();

        $dispatcher->registerHandler('event2', $handler);

        $dispatcher->handle();

        $receivedMessages = $handler->getReceivedMessages();
        $this->assertEquals(1, count($receivedMessages));
        $this->assertEquals('event2', $receivedMessages[0]->getType());
        $this->assertEquals(['c' => 'd'], $receivedMessages[0]->getPayload());
        
    }

    public function testMuplitpleHandlersOnOneEvent()
    {
        $message1 = new Message('eventx', ['a' => 'x']);

        $driver = new DummyDriver([$message1]);
        $dispatcher = new Dispatcher($driver);

        $handler1 = new TestHandler();
        $handler2 = new TestHandler();

        $dispatcher->registerHandler('eventx', $handler1);
        $dispatcher->registerHandler('eventx', $handler2);

        $dispatcher->handle();

        $receivedMessages = $handler1->getReceivedMessages();
        $this->assertEquals(1, count($receivedMessages));
        $this->assertEquals('eventx', $receivedMessages[0]->getType());
        $this->assertEquals(['a' => 'x'], $receivedMessages[0]->getPayload());

        $receivedMessages = $handler2->getReceivedMessages();
        $this->assertEquals(1, count($receivedMessages));
        $this->assertEquals('eventx', $receivedMessages[0]->getType());
        $this->assertEquals(['a' => 'x'], $receivedMessages[0]->getPayload());
    }

    public function testOtherEvent()
    {
        $message1 = new Message('eventx', ['a' => 'x']);
        $message2 = new Message('eventy', ['a' => 'x']);

        $driver = new DummyDriver([$message1, $message2]);
        $dispatcher = new Dispatcher($driver);

        $handler = new TestHandler();

        $dispatcher->registerHandler('unknown', $handler);

        $dispatcher->handle();

        $receivedMessages = $handler->getReceivedMessages();
        $this->assertEquals(0, count($receivedMessages));
    }

    public function testHandlerWithException()
    {
        $message1 = new Message('eventx', ['a' => 'x']);

        $driver = new DummyDriver([$message1]);
        $dispatcher = new Dispatcher($driver);

        $dispatcher->registerHandler('eventx', new ExceptionHandler());
        $dispatcher->handle();
    }
}
