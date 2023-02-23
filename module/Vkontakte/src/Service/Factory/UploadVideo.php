<?php

declare(strict_types=1);

namespace Coderun\Vkontakte\Service\Factory;

use Coderun\Vkontakte\Service\UploadVideo as UploadVideoService;
use Psr\Container\ContainerInterface;
use Symfony\Component\HttpClient\HttpClient;

/**
 * Class UploadVideo
 *
 * @package Coderun\Vkontakte\Service
 */
class UploadVideo
{
    /**
     * Create service
     *
     * @param ContainerInterface   $container
     * @param string               $requestedName
     * @param array<string, mixed> $options
     * @return UploadVideoService
     */
    public function __invoke(
        ContainerInterface $container,
        string $requestedName,
        array $options = []
    ): UploadVideoService {
        return new UploadVideoService(HttpClient::create());
    }
}
