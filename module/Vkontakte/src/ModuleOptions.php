<?php

declare(strict_types=1);

namespace Coderun\Vkontakte;

use Coderun\Vkontakte\Options\Api;
use Laminas\Stdlib\AbstractOptions;

use function is_array;

/**
 * Class ModuleOptions
 *
 * @package Coderun\Vkontakte;
 */
class ModuleOptions extends AbstractOptions
{
    /** @var Api  */
    protected Api $api;

    /**
     * Get api
     *
     * @return Api
     */
    public function getApi(): Api
    {
        return $this->api;
    }
    
    /**
     * @param Api $api
     *
     * @return ModuleOptions
     */
    protected function setApi(mixed $api): ModuleOptions
    {
        if (!$api instanceof Api) {
            $api = new Api($api);
        }
        $this->api = $api;
        return $this;
    }
    
    
}
