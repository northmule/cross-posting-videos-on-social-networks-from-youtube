<?php

declare(strict_types=1);

namespace Coderun\RuTube\ValueObject;

use Coderun\RuTube\Options\Api;

use function sprintf;

/**
 * Class Authorization
 *
 * @package Coderun\RuTube\ValueObject
 */
class Authorization
{
    /** @var string  */
    protected string $token;
    /** @var string  */
    protected string $author;

    /**
     * @param Api $config
     */
    public function __construct(Api $config)
    {
        $this->token = $config->getToken();
        $this->author = $config->getUserId();
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
     * Get author
     *
     * @return string
     */
    public function getAuthor(): string
    {
        return $this->author;
    }


    /**
     * @return string
     */
    public function getAuthorizationStringForHeader(): string
    {
        return sprintf('Token %s', $this->token);
    }
}
