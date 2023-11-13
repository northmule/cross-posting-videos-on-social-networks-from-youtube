<?php

declare(strict_types=1);

namespace Coderun\Vkontakte;

/**
 * ConfigProvider
 */
class ConfigProvider
{
    /** @var string  */
    public const CONFIG_KEY = 'vkontakte_config';

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
            'invokables' => [
                \GuzzleHttp\Client::class,
            ],
            'auto' => [
                \Coderun\Youtube\ContentAdapter\PresaverCom::class,
                \Coderun\Vkontakte\Service\UploadVideo::class,
            ],
            'factories'  => [
                ModuleOptions::class                          => ModuleOptionsFactory::class,
                \Coderun\Vkontakte\Handler\UploadVideo::class => \Coderun\Vkontakte\Handler\Factory\UploadVideo::class,

            ],
        ];
    }
}
