<?php

declare(strict_types=1);

/**
 * Перенос всех видео с Ютуб в ВК
 * Скрипт создаст первоначальную базу и сохранит данные в файл, и при повторном запуске будет брать ранее созданную базу
 */

use Coderun\Common\Collection\VideoMap;
use Coderun\Common\ModuleOptions as CommonModuleOptions;
use Coderun\Common\Service\History;
use Coderun\Common\ValueObject\Video;
use Coderun\Vkontakte\Handler\UploadVideo as UploadVideo;
use Psr\Container\ContainerInterface;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Northmule\Container\ContainerUnit;

chdir(dirname(__DIR__));

require 'vendor/autoload.php';

/** @var ContainerInterface  $container */
$container = require 'config/container.php';

final class FullTransferOfVideosFromYoutubeToVk
{
    /** @var string  */
    protected const DB_NAME = 'toVk.data';
    /** @var UploadVideo */
    protected UploadVideo $vk;
    /** @var Logger  */
    protected Logger $logger;
    /** @var History  */
    protected History $history;
    /** @var CommonModuleOptions */
    protected CommonModuleOptions $commonOptions;
    
    /**
     * @param ContainerUnit $container
     *
     * @return void
     * @throws Exception
     */
    public function __invoke(ContainerInterface $container)
    {
        try {
            $this->history = $container->get(History::class);
            $this->commonOptions = $container->get(CommonModuleOptions::class);
            $this->logger = new Logger('script');
            $this->logger->pushHandler(
                new StreamHandler(
                    sprintf('%s/full-transfer-of-videos-from-youtube-to-vk.log', $this->commonOptions->getApi()->getDirLog()),
                    Logger::DEBUG)
            );
            $youtube = $container->get(\Coderun\Youtube\Service\FindVideo::class);
            $this->vk = $container->get(UploadVideo::class);
            
            $this->logger->info('Start processing');
            if (!$this->history->fileExists(self::DB_NAME)) {
                $videosMap = $youtube->findAll();
                $this->history->save(self::DB_NAME, serialize($videosMap));
            } else {
                $videosMapData = $this->history->getFileContent(self::DB_NAME);
                $videosMap = unserialize($videosMapData);
            }
            $this->logger->info('YouTube video count: '. $videosMap->count());
            /**
             * @var string $videoId
             * @var Video  $video
             */
            foreach ($videosMap as $video) {
                 $this->handler($video);
            }
        } catch (Throwable $e) {
            $this->logger->error('Catch throwable: ');
            $this->logger->error($e->getMessage(), ['trace' => $e->getTrace()]);
            throw $e;
        } finally {
            $this->logger->info('Finally processing');
        }
        
    }
    
    
    /**
     * @param Video $video
     *
     * @return void
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    protected function handler(Video $video): void
    {
        $dbPatch = 'full_handlVk.db';
        $dbCount = $this->history->contentCount($dbPatch);
        $this->logger->info('DB video count: '.$dbCount);
        if ($this->history->contentExists($dbPatch, $video->getVideoId())) {
            $this->logger->info('Skip handleRutube: '.$video->getVideoId());
            return;
        }
        $this->logger->info('Start: handleRutube: '.$video->getVideoId());
        $response = $this->vk->upload($video);
        if ($response['video_hash']) {
            $this->history->save($dbPatch, $video->getVideoId());
            echo 'Success: '.$video->getVideoId().PHP_EOL;
        }
        $this->logger->info('End: handleRutube', ['response' => $response]);
    }
    
}

try {
    (new FullTransferOfVideosFromYoutubeToVk())($container);
} catch (Throwable $e) {
    echo $e->getMessage();
    if (str_contains($e->getMessage(), 'cURL error')) {
        echo 'Error:'. $e::class.PHP_EOL;
        echo 'Restart'.PHP_EOL;
        (new FullTransferOfVideosFromYoutubeToVk())($container);
    }
}
