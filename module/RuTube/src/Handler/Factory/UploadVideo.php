<?php

declare(strict_types=1);

namespace Coderun\RuTube\Handler\Factory;

use Coderun\RuTube\ModuleOptions;
use Coderun\RuTube\Service\UploadVideo as UploadVideoService;
use Coderun\RuTube\ValueObject\Authorization;
use Psr\Container\ContainerInterface;
use Coderun\RuTube\Handler\UploadVideo as UploadVideoHandler;

/**
 * Class UploadVideo
 *
 * @package Coderun\RuTube\Handler
 */
class UploadVideo
{
    /**
     * Create service
     *
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param array<mixed>       $options
     * @return UploadVideoHandler
     */
    public function __invoke(
        ContainerInterface $container,
        string $requestedName,
        array $options = []
    ): UploadVideoHandler {
        /** @var ModuleOptions $config */
        $config = $container->get(ModuleOptions::class);
        $authorization = new Authorization($config->getApi());
        $service = $container->get(UploadVideoService::class);
        return new UploadVideoHandler($authorization, $service);
    }
}