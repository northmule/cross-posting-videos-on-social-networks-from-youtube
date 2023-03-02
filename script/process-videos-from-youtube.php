<?php

declare(strict_types=1);

/**
 * Забирает видео из ютуб и постит их:
 *  - Телеграм
 *  - Рутуб
 *  - Вконтакте (группа)
 */

use Coderun\Common\ModuleOptions as CommonModuleOptions;
use Coderun\Common\Service\History;
use Coderun\Common\ValueObject\Video;
use Coderun\Container\ContainerUnit;
use Coderun\RuTube\Handler\UploadVideo as UploadVideoRutube;
use Coderun\Telegram\Handler\UploadVideo as UploadVideoTelegram;
use Coderun\Vkontakte\Handler\UploadVideo as UploadVideoVk;
use Psr\Container\ContainerInterface;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

chdir(dirname(__DIR__));

require 'vendor/autoload.php';

/** @var ContainerInterface  $container */
$container = require 'config/container.php';

final class ProcessVideosFromYoutube
{
    /** @var UploadVideoVk */
    protected UploadVideoVk $vkontakte;
    /** @var UploadVideoTelegram */
    protected UploadVideoTelegram $telegram;
    /** @var UploadVideoRutube */
    protected UploadVideoRutube $rutube;
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
        $this->history = $container->get(History::class);
        $this->commonOptions = $container->get(CommonModuleOptions::class);
        $this->logger = new Logger('script');
        $this->logger->pushHandler(
            new StreamHandler(
                sprintf('%s/process-videos-from-youtube.log', $this->commonOptions->getApi()->getDirLog()),
                Logger::DEBUG)
        );
        
        $youtube = $container->get(\Coderun\Youtube\Service\FindVideo::class);
        $this->vkontakte = $container->get(UploadVideoVk::class);
        $this->telegram = $container->get(UploadVideoTelegram::class);
        $this->rutube = $container->get(UploadVideoRutube::class);
        try {
            $this->logger->info('Start processing');
            $videosMap = $youtube->find();
            $this->logger->info('YouTube video count: '. $videosMap->count());
            /**
             * @var string $videoId
             * @var Video  $video
             */
            foreach ($videosMap as $video) {
                $this->handleVkontakte($video);
                $this->handleTelegram($video);
                $this->handleRutube($video);
            }
        } catch (Throwable $e) {
            $this->logger->error('Catch throwable: ');
            $this->logger->error($e->getMessage(), ['trace' => $e->getTrace()]);
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
    protected function handleVkontakte(Video $video) {
        if ($this->history->contentExists('handleVkontakte.db', $video->getVideoId())) {
            $this->logger->info('Skip handleVkontakte: '.$video->getVideoId());
            return;
        }
        $this->logger->info('Start: handleVkontakte '.$video->getVideoId());
        $response = $this->vkontakte->upload($video);
        if ($response['response']) {
            $this->history->save('handleVkontakte.db', $video->getVideoId());
        }
        $this->logger->info('End: handleVkontakte', ['response' => $response]);
    }

    /**
     * @param Video $video
     *
     * @return void
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    protected function handleTelegram(Video $video) {
        $dbPatch = 'handleTelegram.db';
        if ($this->history->contentExists($dbPatch, $video->getVideoId())) {
            $this->logger->info('Skip handleTelegram: '.$video->getVideoId());
            return;
        }
        $this->logger->info('Start: handleTelegram '.$video->getVideoId());
        $response = $this->telegram->upload($video);
        if ($response['ok']) {
            $this->history->save($dbPatch, $video->getVideoId());
        }
        $this->logger->info('End: handleTelegram', ['response' => $response]);
    }
    
    /**
     * @param Video $video
     *
     * @return void
     */
    protected function handleRutube(Video $video) {
        $dbPatch = 'handleRutube.db';
        if ($this->history->contentExists($dbPatch, $video->getVideoId())) {
            $this->logger->info('Skip handleRutube: '.$video->getVideoId());
            return;
        }
        $this->logger->info('Start: handleRutube '.$video->getVideoId());
        $response = $this->rutube->upload($video);
        if ($response['video_id']) {
            $this->history->save($dbPatch, $video->getVideoId());
        }
        $this->logger->info('End: handleRutube', ['response' => $response]);
    }
    
}

(new ProcessVideosFromYoutube())($container);
