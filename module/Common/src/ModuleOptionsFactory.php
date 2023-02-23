<?php

declare(strict_types=1);

namespace Coderun\Common;

use Psr\Container\ContainerInterface;

/**
 * Class ModuleOptionsFactory
 *
 * @package Coderun\Common
 */
class ModuleOptionsFactory
{
    /**
     * Create service
     *
     * @param ContainerInterface   $container
     * @param string               $requestedName
     * @param array<string, mixed> $options
     * @return ModuleOptions
     */
    public function __invoke(
        ContainerInterface $container,
        string $requestedName,
        array $options = []
    ): ModuleOptions {
        /** @phpstan-ignore-next-line  */
        return new ModuleOptions($container->get('config')[ConfigProvider::CONFIG_KEY] ?? []);
    }
}
