<?php

declare(strict_types=1);

namespace Coderun\Youtube\Options;

use Laminas\Stdlib\AbstractOptions;

/**
 * Class Api
 *
 * @package Coderun\Youtube\Options
 */
class Api extends AbstractOptions
{
    /** @var array<int, string>  */
    protected array $channels;
    /** @var string  */
    protected string $token;
    /** @var string  */
    protected string $appName;
    /** @var string  */
    protected string $maxResult;
    /** @var string  */
    protected string $order;

    /**
     * Get channels
     *
     * @return array<int, string>
     */
    public function getChannels(): array
    {
        return $this->channels;
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
     * Get appName
     *
     * @return string
     */
    public function getAppName(): string
    {
        return $this->appName;
    }

    /**
     * Get maxResult
     *
     * @return string
     */
    public function getMaxResult(): string
    {
        return $this->maxResult;
    }

    /**
     * Get order
     *
     * @return string
     */
    public function getOrder(): string
    {
        return $this->order;
    }

    /**
     * @param string $channels
     *
     * @return Api
     */
    protected function setChannels(string $channels): Api
    {
        $this->channels = explode(',', $channels);
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

    /**
     * @param string $appName
     *
     * @return Api
     */
    protected function setAppName(string $appName): Api
    {
        $this->appName = $appName;
        return $this;
    }

    /**
     * @param string $maxResult
     *
     * @return Api
     */
    protected function setMaxResult(string $maxResult): Api
    {
        $this->maxResult = $maxResult;
        return $this;
    }

    /**
     * @param string $order
     *
     * @return Api
     */
    protected function setOrder(string $order): Api
    {
        $this->order = $order;
        return $this;
    }
}
