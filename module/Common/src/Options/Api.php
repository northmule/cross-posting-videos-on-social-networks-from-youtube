<?php

declare(strict_types=1);

namespace Coderun\Common\Options;

use Laminas\Stdlib\AbstractOptions;

/**
 * Class Api
 *
 * @package Coderun\Common\Options
 */
class Api extends AbstractOptions
{
    /** @var string  */
    protected string $dirHistory;
    /** @var string  */
    protected string $dirLog;

    /**
     * Get dirHistory
     *
     * @return string
     */
    public function getDirHistory(): string
    {
        return $this->dirHistory;
    }

    /**
     * Get dirLog
     *
     * @return string
     */
    public function getDirLog(): string
    {
        return $this->dirLog;
    }

    /**
     * @param string $dirHistory
     *
     * @return Api
     */
    protected function setDirHistory(string $dirHistory): Api
    {
        $this->dirHistory = $dirHistory;
        return $this;
    }

    /**
     * @param string $dirLog
     *
     * @return Api
     */
    protected function setDirLog(string $dirLog): Api
    {
        $this->dirLog = $dirLog;
        return $this;
    }
}
