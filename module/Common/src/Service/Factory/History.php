<?php

declare(strict_types=1);

namespace Coderun\Common\Service\Factory;

use Coderun\Common\Service\History as HistoryService;
use Psr\Container\ContainerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Coderun\Common\ModuleOptions as CommonModuleOptions;

/**
 * Class UploadVideo
 *
 * @package Coderun\Common\Service
 */
class History
{
    /**
     * Create service
     *
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param array              $options
     * @return HistoryService
     */
    public function __invoke(
        ContainerInterface $container,
        string $requestedName,
        array $options = []
    ): HistoryService {
        $commonOptions = $container->get(CommonModuleOptions::class);

        return new HistoryService(new Filesystem(), $commonOptions->getApi()->getDirHistory() ?? '');
    }
}
