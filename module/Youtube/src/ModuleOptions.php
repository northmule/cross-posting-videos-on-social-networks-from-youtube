<?php

declare(strict_types=1);

namespace Coderun\Youtube;

use Coderun\Youtube\Options\Api;
use Laminas\Stdlib\AbstractOptions;

use function is_array;

/**
 * Class ModuleOptions
 *
 * @package Coderun\Youtube;
 */
class ModuleOptions extends AbstractOptions
{
    /** @var Api  */
    protected Api $api;

    /**
     * ModuleOptions constructor.
     *
     * @param array|\Traversable<mixed>|null $options
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
