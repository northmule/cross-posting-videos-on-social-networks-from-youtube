<?php

declare(strict_types=1);

namespace Coderun\Youtube;

/**
 * ConfigProvider
 */
class ConfigProvider
{
    /** @var string  */
    public const CONFIG_KEY = 'youtube_config';

    /**
     * @return array<string, array<array<string>>>
     */
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencies(),
        ];
    }

    /**
     * @return array<string, array<string,string>>
     */
    public function getDependencies(): array
    {
        return [
            'invokables' => [],
            'reflection' => [],
            'factories'  => [
                ModuleOptions::class                      => ModuleOptionsFactory::class,
                \Coderun\Youtube\Service\FindVideo::class => \Coderun\Youtube\Service\Factory\FindVideo::class,
            ],
        ];
    }
}
