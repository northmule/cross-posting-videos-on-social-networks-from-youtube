<?php

declare(strict_types=1);

namespace Coderun\Common;

/**
 * ConfigProvider
 */
class ConfigProvider
{
    /** @var string  */
    public const CONFIG_KEY = 'common_config';

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
                ModuleOptions::class                   => ModuleOptionsFactory::class,
                \Coderun\Common\Service\History::class => \Coderun\Common\Service\Factory\History::class,
            ],
        ];
    }
}
