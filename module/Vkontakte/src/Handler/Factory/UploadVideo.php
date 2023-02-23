<?php

declare(strict_types=1);

namespace Coderun\Vkontakte\Handler\Factory;

use Coderun\Vkontakte\ModuleOptions;
use Coderun\Vkontakte\Service\UploadVideo as UploadVideoService;
use Coderun\Vkontakte\ValueObject\Authorization;
use Psr\Container\ContainerInterface;
use Coderun\Vkontakte\Handler\UploadVideo as UploadVideoHandler;

/**
 * Class UploadVideo
 *
 * @package Coderun\Vkontakte\Handler
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
        /** @var UploadVideoService $service */
        $service = $container->get(UploadVideoService::class);
        return new UploadVideoHandler($authorization, $service);
    }
}
