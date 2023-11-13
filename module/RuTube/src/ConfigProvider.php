<?php

declare(strict_types=1);

namespace Coderun\RuTube;

/**
 * ConfigProvider
 */
class ConfigProvider
{
    /** @var string  */
    public const CONFIG_KEY = 'ru_tube_config';

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
            'auto' => [
                \Coderun\RuTube\Service\UploadVideo::class,
            ],
            'factories'  => [
                ModuleOptions::class                       => ModuleOptionsFactory::class,
               // \Coderun\RuTube\Service\UploadVideo::class => \Coderun\RuTube\Service\Factory\UploadVideo::class,
                \Coderun\RuTube\Handler\UploadVideo::class => \Coderun\RuTube\Handler\Factory\UploadVideo::class,
            ],
        ];
    }
}
