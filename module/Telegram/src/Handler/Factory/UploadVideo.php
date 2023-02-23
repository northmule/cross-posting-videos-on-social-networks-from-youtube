<?php

declare(strict_types=1);

namespace Coderun\Telegram\Handler\Factory;

use Coderun\Telegram\ModuleOptions;
use Coderun\Telegram\Service\UploadVideo as UploadVideoService;
use Coderun\Telegram\ValueObject\Authorization;
use Psr\Container\ContainerInterface;
use Coderun\Telegram\Handler\UploadVideo as UploadVideoHandler;

/**
 * Class UploadVideo
 *
 * @package Coderun\Telegram\Handler
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
