<?php

namespace Ludoi\Logger;

class Logger
{
    /** @var array */
    private $channels = array();

    private $handler;

    public function __construct(Handler\AbstractHandler $handler)
    {
        $this->handler = $handler;
    }

    public function getHandler(): Handler\AbstractHandler
    {
        return $this->handler;
    }

    public function getChannel(string $channel): LoggerChannel
    {
        if (!in_array($channel, $this->channels)) {
            $this->channels[$channel] = new LoggerChannel($channel, $this);
        }
        return $this->channels[$channel];
    }
}
