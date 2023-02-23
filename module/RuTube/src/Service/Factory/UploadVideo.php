<?php

declare(strict_types=1);

namespace Coderun\RuTube\Service\Factory;

use Coderun\RuTube\Service\UploadVideo as UploadVideoService;
use Psr\Container\ContainerInterface;
use Symfony\Component\HttpClient\HttpClient;

/**
 * Class UploadVideo
 *
 * @package Coderun\RuTube\Service
 */
class UploadVideo
{
    /**
     * Create service
     *
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param array              $options
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
