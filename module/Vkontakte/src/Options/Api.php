<?php

declare(strict_types=1);

namespace Coderun\Vkontakte\Options;

use Laminas\Stdlib\AbstractOptions;

/**
 * Class Api
 *
 * @package Coderun\Vkontakte\Options
 */
class Api extends AbstractOptions
{
    /** @var string  */
    protected string $groupId;
    /** @var string  */
    protected string $token;
    /** @var string  */
    protected string $albumId;
    /** @var string  */
    protected string $version;
    
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
     * Get token
     *
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
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
    
    /**
     * Get version
     *
     * @return string
     */
    public function getVersion(): string
    {
        return $this->version;
    }
    
    /**
     * @param string $groupId
     *
     * @return Api
     */
    protected function setGroupId(string $groupId): Api
    {
        $this->groupId = $groupId;
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
     * @param string $albumId
     *
     * @return Api
     */
    protected function setAlbumId(string $albumId): Api
    {
        $this->albumId = $albumId;
        return $this;
    }
    
    /**
     * @param string $version
     *
     * @return Api
     */
    protected function setVersion(string $version): Api
    {
        $this->version = $version;
        return $this;
    }
    
    
}