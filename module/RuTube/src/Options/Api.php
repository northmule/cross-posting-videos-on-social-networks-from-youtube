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
     * @param string|null $token
     *
     * @return Api
     */
    protected function setToken(?string $token): Api
    {
        $this->token = $token ?? '';
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
     * @param string|null $userId
     *
     * @return Api
     */
    protected function setUserId(?string $userId): Api
    {
        $this->userId = $userId ?? '';
        return $this;
    }
}
