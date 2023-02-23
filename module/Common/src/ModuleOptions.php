<?php

declare(strict_types=1);

namespace Coderun\Common;

use Coderun\Common\Options\Api;
use Laminas\Stdlib\AbstractOptions;

use function is_array;

/**
 * Class ModuleOptions
 *
 * @package Coderun\Common;
 */
class ModuleOptions extends AbstractOptions
{
    /** @var Api  */
    protected Api $api;

    /**
     * ModuleOptions constructor.
     *
     * @param null|array<string, mixed> $options
     */
    public function __construct($options = null)
    {
        $this->api = new Api($options);
    }

    /**
     * Get api
     *
     * @return Api
     */
    public function getApi(): Api
    {
        return $this->api;
    }
}
