<?php

declare(strict_types=1);

namespace Coderun\Youtube\Service;

use Coderun\Common\ValueObject\Video;
use Coderun\Youtube\ValueObject\SearchParams;
use Coderun\Common\ValueObject\Video as YoutubeVideo;
use Coderun\Common\Collection\VideoMap;
use Google_Service_YouTube;
use Google_Service_YouTube_SearchResult;
use Google_Service_YouTube_Video;
use YouTube\Exception\TooManyRequestsException;
use YouTube\Exception\YouTubeException;
use YouTube\Models\StreamFormat;
use YouTube\YouTubeDownloader;

use function array_pop;
use function implode;
use function sprintf;

/**
 * Class FindVideo
 *
 * @package Coderun\Youtube\Service
 */
class FindVideo
{
    /** @var string  */
    protected const YOUTUBE_VIDEO_URL = 'https://www.youtube.com/watch?v=%s';
    
    /** @var Google_Service_YouTube  */
    protected Google_Service_YouTube $googleService;
    /** @var SearchParams  */
    protected SearchParams $searchParams;
    protected YouTubeDownloader $youTubeDownloader;
    
    /**
     * @param Google_Service_YouTube $googleService
     * @param SearchParams           $searchParams
     * @param YouTubeDownloader      $youTubeDownloader
     */
    public function __construct(
        Google_Service_YouTube $googleService,
        SearchParams $searchParams,
        YouTubeDownloader $youTubeDownloader
    ) {
        $this->googleService = $googleService;
        $this->searchParams = $searchParams;
        $this->youTubeDownloader = $youTubeDownloader;
    }
    
    /**
     *  Сделать запрос с API и вернёт коллекцию видео
     *
     * @return VideoMap<string, Video>
     * @throws TooManyRequestsException
     * @throws YouTubeException
     */
    public function find(): VideoMap
    {
        $videoMap = new VideoMap();
        $videos = $this->googleService->videos->listVideos(
            'id',
            ['id' => implode(',', $this->getVideoIds())]
        );
        /** @var Google_Service_YouTube_Video $video */
        foreach ($videos as $video) {
            $downloadLinks = $this->youTubeDownloader->getDownloadLinks(sprintf(self::YOUTUBE_VIDEO_URL, $video->getId()));
            $directLink = null;
            if ($downloadLinks->getAllFormats()) {
                $videFormats = $downloadLinks->getCombinedFormats();
                /** @var StreamFormat $videoHighQuality */
                $videoHighQuality = array_pop($videFormats);
                $directLink = $videoHighQuality->url ?? null;
            }
            if ($directLink === null) {
                // todo запись в лог
                continue;
            }
            $videoMap->offsetSet(
                $video->getId(),
                new YoutubeVideo($directLink, $downloadLinks->getInfo())
            );
        }
        
        return $videoMap;
    }
    
    /**
     * Список ид видео
     *
     * @return array<int, string>
     */
    protected function getVideoIds(): array
    {
        $listSearch = $this->googleService->search->listSearch(
            'id',
            $this->searchParams->getForFirstChannel()
        );
        $videosIds = [];
        /** @var Google_Service_YouTube_SearchResult $item */
        foreach ($listSearch->getItems() as $item) {
            $videosIds[] = $item->getId()->getVideoId();
        }
        return $videosIds;
    }
}