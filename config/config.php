<?php

declare(strict_types=1);

use Laminas\ConfigAggregator\ArrayProvider;
use Laminas\ConfigAggregator\ConfigAggregator;
use Laminas\ConfigAggregator\PhpFileProvider;

$cacheConfig = ['config_cache_path' => 'data/cache/config-cache.php'];
$aggregator = new ConfigAggregator([
    new ArrayProvider($cacheConfig),
    // Зависимости
    
    // Модули приложения
    Coderun\Common\ConfigProvider::class,
    Coderun\RuTube\ConfigProvider::class,
    Coderun\Vkontakte\ConfigProvider::class,
    Coderun\Telegram\ConfigProvider::class,
    Coderun\Youtube\ConfigProvider::class,
    new PhpFileProvider(realpath(__DIR__) . '/autoload/{{,*.}global,{,*.}local}.php'),
],$cacheConfig['config_cache_path']);

return $aggregator->getMergedConfig();
