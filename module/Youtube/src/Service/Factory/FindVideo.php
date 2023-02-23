<?php

declare(strict_types=1);

namespace Coderun\Youtube\Service\Factory;

use Coderun\Youtube\ModuleOptions;
use Coderun\Youtube\Service\FindVideo as FindVideoService;
use Coderun\Youtube\ValueObject\SearchParams;
use Google\Client as GoogleClient;
use Google_Service_YouTube;
use Psr\Container\ContainerInterface;
use YouTube\YouTubeDownloader;

/**
 * Class FindVideo
 *
 * @package Coderun\Youtube\Service\Factory
 */
class FindVideo
{
    /**
     * Create service
     *
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param array              $options
     * @return FindVideoService
     */
    public function __invoke(
        ContainerInterface $container,
        string $requestedName,
        array $options = []
    ): FindVideoService {
        $moduleOptions = $container->get(ModuleOptions::class);
        $client = new GoogleClient();
        $client->setApplicationName($moduleOptions->getApi()->getAppName());
        $client->setDeveloperKey($moduleOptions->getApi()->getToken());
        $googleService = new Google_Service_YouTube($client);
        $searchParams = new SearchParams($moduleOptions->getApi());
        return new FindVideoService($googleService, $searchParams, new YouTubeDownloader());
    }
}
