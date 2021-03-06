<?php

namespace Tomaj\Hermes;

use Tomaj\Hermes\Handler\HandlerInterface;

interface DispatcherInterface
{
    /**
     * Emit new message
     *
     * @param MessageInterface  $message
     *
     * @return $this
     */
    public function emit(MessageInterface $message);


    /**
     * Register new handler
     *
     * With this method you can register new handler for selcted $type.
     * This handler will be called in background job when event
     * of registered $type will be emited.
     *
     * @param string             $type
     * @param HandlerInterface   $handler
     *
     * @return $this
     */
    public function registerHandler($type, HandlerInterface $handler);
}
