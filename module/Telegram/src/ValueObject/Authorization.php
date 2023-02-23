<?php

declare(strict_types=1);

namespace Coderun\Telegram\ValueObject;

use Coderun\Telegram\Options\Api;

/**
 * Class Authorization
 *
 * @package Coderun\Telegram\ValueObject
 */
class Authorization
{
    /** @var string  */
    protected string $token;
    /** @var string  */
    protected string $chanel;

    /**
     * @param Api $config
     */
    public function __construct(Api $config)
    {
        $this->token = $config->getToken();
        $this->chanel = $config->getChannel();
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
     * Get chanel
     *
     * @return string
     */
    public function getChanel(): string
    {
        return $this->chanel;
    }
}
