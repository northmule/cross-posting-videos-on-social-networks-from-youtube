<?php

declare(strict_types=1);

namespace Coderun\Telegram\Options;

use Laminas\Stdlib\AbstractOptions;

/**
 * Class Api
 *
 * @package Coderun\Telegram\Options
 */
class Api extends AbstractOptions
{
    /** @var string  */
    protected string $channel;
    /** @var string  */
    protected string $token;
    
    /**
     * Get channel
     *
     * @return string
     */
    public function getChannel(): string
    {
        return $this->channel;
    }
    
    /**
     * Get token
     *
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }
    
    /**
     * @param string $channel
     *
     * @return Api
     */
    protected function setChannel(string $channel): Api
    {
        $this->channel = $channel;
        return $this;
    }
    
    /**
     * @param string $token
     *
     * @return Api
     */
    protected function setToken(string $token): Api
    {
        $this->token = $token;
        return $this;
    }
    
    
}