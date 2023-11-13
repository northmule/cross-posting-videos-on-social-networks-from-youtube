<?php

declare(strict_types=1);

namespace Coderun\Telegram;

/**
 * ConfigProvider
 */
class ConfigProvider
{
    /** @var string  */
    public const CONFIG_KEY = 'telegram_config';

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
                \Coderun\Telegram\Service\UploadVideo::class,
            ],
            'factories'  => [
                ModuleOptions::class                         => ModuleOptionsFactory::class,
                \Coderun\Telegram\Handler\UploadVideo::class => \Coderun\Telegram\Handler\Factory\UploadVideo::class,

            ],
        ];
    }
}
