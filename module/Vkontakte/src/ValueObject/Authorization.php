<?php

declare(strict_types=1);

namespace Coderun\Vkontakte\ValueObject;

use Coderun\Vkontakte\Options\Api;

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
    protected string $groupId;
    /** @var string  */
    protected string $apiVersion;
    /** @var string  */
    protected string $albumId;
    
    public function __construct(Api $config)
    {
        $this->token = $config->getToken();
        $this->groupId = $config->getGroupId();
        $this->apiVersion = $config->getVersion();
        $this->albumId = $config->getAlbumId();
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
     * Get groupId
     *
     * @return string
     */
    public function getGroupId(): string
    {
        return $this->groupId;
    }
    
    /**
     * Get apiVersion
     *
     * @return string
     */
    public function getApiVersion(): string
    {
        return $this->apiVersion;
    }
    
    /**
     * Get albumId
     *
     * @return string
     */
    public function getAlbumId(): string
    {
        return $this->albumId;
    }
    
    
}