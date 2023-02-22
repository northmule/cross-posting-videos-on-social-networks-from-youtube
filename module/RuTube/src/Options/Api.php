<?php

declare(strict_types=1);

namespace Coderun\RuTube\Options;

use Laminas\Stdlib\AbstractOptions;

use function strval;

/**
 * Class Api
 *
 * @package Coderun\RuTube\Options
 */
class Api extends AbstractOptions
{
    /** @var string  */
    protected string $token;
    /** @var string  */
    protected string $userId;
    
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
     * @param string $token
     *
     * @return Api
     */
    protected function setToken($token): Api
    {
        $this->token = strval($token ?? '');
        return $this;
    }
    
    /**
     * Get userId
     *
     * @return string
     */
    public function getUserId(): string
    {
        return $this->userId;
    }
    
    /**
     * @param string $userId
     *
     * @return Api
     */
    protected function setUserId($userId): Api
    {
        $this->userId = strval($userId ?? '');
        return $this;
    }
    
    
}