<?php

declare(strict_types=1);

/**
 * Перенос всех видео с Ютуб в Рутуб
 * Скрипт создаст первоначальную базу и сохранит данные в файл, и при повторном запуске будет брать ранее созданную базу
 * Рутуб не даёт загрузить большое количество видео, по этому скрипт будет прерываться. Его необходимо запускать повторно.
 * На момент написания скрипта, рутуб не даёт добавлять в очередь на модерацию более 25-30 видео, после того как пачка отмодерируется - можно отправлять следующую порцию
 */

use Coderun\Common\Collection\VideoMap;
use Coderun\Common\ModuleOptions as CommonModuleOptions;
use Coderun\Common\Service\History;
use Coderun\Common\ValueObject\Video;
use Coderun\Container\ContainerUnit;
use Coderun\RuTube\Handler\UploadVideo as UploadVideoRutube;
use Psr\Container\ContainerInterface;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

chdir(dirname(__DIR__));

require 'vendor/autoload.php';

/** @var ContainerInterface  $container */
$container = require 'config/container.php';

final class FullTransferOfVideosFromYoutubeToRutub
{
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
                sprintf('%s/full-transfer-of-videos-from-youtube-to-rutub.log', $this->commonOptions->getApi()->getDirLog()),
                Logger::DEBUG)
        );
        
        $youtube = $container->get(\Coderun\Youtube\Service\FindVideo::class);
        $this->rutube = $container->get(UploadVideoRutube::class);
        try {
            $this->logger->info('Start processing');
            if (!$this->history->fileExists('toRutube.data')) {
                $videosMap = $youtube->findAll();
                $this->history->save('toRutube.data', $videosMap->serialize());
            } else {
                $videosMapData = $this->history->getFileContent('toRutube.data');
                $videosMap = new VideoMap(unserialize($videosMapData));
            }
            $this->logger->info('YouTube video count: '. $videosMap->count());
            /**
             * @var string $videoId
             * @var Video  $video
             */
            foreach ($videosMap as $video) {
                $this->handleRutube($video);
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
    protected function handleRutube(Video $video) {
        static $cntVideo = 0;
        $dbPatch = 'full_handleRutube.db';
        $dbCount = $this->history->contentCount($dbPatch);
        $this->logger->info('DB video count: '.$dbCount);
        if ($this->history->contentExists($dbPatch, $video->getVideoId())) {
            $this->logger->info('Skip handleRutube: '.$video->getVideoId());
            return;
        }
        $this->logger->info('Start: handleRutube: '.$video->getVideoId());
        $response = $this->rutube->upload($video);
        if ($response['video_id']) {
            $this->history->save($dbPatch, $video->getVideoId());
        }
        $this->logger->info('End: handleRutube', ['response' => $response]);
        $cntVideo++;
        if ($cntVideo == 25) {
            $cntVideo = 0;
            throw new RuntimeException('Interrupted by limit');
        }
    }
    
}

(new FullTransferOfVideosFromYoutubeToRutub())($container);
