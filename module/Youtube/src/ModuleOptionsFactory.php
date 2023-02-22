<?php

declare(strict_types=1);

namespace Coderun\Youtube;

use Psr\Container\ContainerInterface;

/**
 * Class ModuleOptionsFactory
 *
 * @package Coderun\Youtube
 */
class ModuleOptionsFactory
{
    /**
     * Create service
     *
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param array<mixed>       $options
     * @return ModuleOptions
     */
    public function __invoke(
        ContainerInterface $container,
        string $requestedName,
        array $options = []
    ): ModuleOptions {
        return new ModuleOptions($container->get('config')[ConfigProvider::CONFIG_KEY] ?? []);
    }
}
