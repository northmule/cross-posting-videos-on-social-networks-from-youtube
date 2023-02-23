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
     * @return array<string,array>
     */
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencies(),
        ];
    }

    /**
     * @return \string[][]
     */
    public function getDependencies(): array
    {
        return [
            'invokables' => [],
            'reflection' => [],
            'factories'  => [
                ModuleOptions::class                         => ModuleOptionsFactory::class,
                \Coderun\Telegram\Service\UploadVideo::class => \Coderun\Telegram\Service\Factory\UploadVideo::class,
                \Coderun\Telegram\Handler\UploadVideo::class => \Coderun\Telegram\Handler\Factory\UploadVideo::class,

            ],
        ];
    }
}
