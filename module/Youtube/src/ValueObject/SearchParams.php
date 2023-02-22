<?php

declare(strict_types=1);

namespace Coderun\Youtube\ValueObject;

use Coderun\Youtube\Options\Api;

use function intval;

/**
 * Class SearchParams
 *
 * @package Coderun\Youtube\ValueObject
 */
class SearchParams
{
    /** @var array<int, string>  */
    protected array $channels;
    /** @var string  */
    protected string $maxResults;
    /** @var string  */
    protected string $order;
    
    public function __construct(Api $api)
    {
        $this->channels = $api->getChannels();
        $this->maxResults = $api->getMaxResult();
        $this->order = $api->getOrder();
    }
    
    /**
     * Параметры поиска для первого канала
     *
     * @return array
     */
    public function getForFirstChannel(): array
    {
        return [
            'channelId' => $this->channels[0] ?? null,
            'maxResults' => intval($this->maxResults),
            'order' => $this->order,
        ];
    }
    
    /**
     * Массив с списком параметров для каждого канала
     *
     * @return array<int, array>
     */
    public function getForAllChannels(): array
    {
        $params = [];
        
        foreach ($this->channels as $channel) {
            $params[] = [
                'channelId' => $channel,
                'maxResults' => intval($this->maxResults),
                'order' => $this->order,
            ];
        }
        return $params;
    }
    
    
}