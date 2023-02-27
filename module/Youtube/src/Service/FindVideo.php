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
use function array_chunk;
use function array_filter;

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
    /** @var YouTubeDownloader  */
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
            $downloadLinks = $this->youTubeDownloader->getDownloadLinks(
                sprintf(self::YOUTUBE_VIDEO_URL, $video->getId())
            );
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
     * Все видео с канала
     *
     * @return VideoMap
     * @throws TooManyRequestsException
     * @throws YouTubeException
     */
    public function findAll(int $batch = 5): VideoMap
    {
        $videoIds = $this->getVideoIdsWithParams();
        $videosList = [];
//        foreach (array_chunk($videoIds, 25) as $chunkIds) {
//            $videosList[] = $this->googleService->videos->listVideos( // todo лишний запрос
//                'id',
//                ['id' => implode(',', $chunkIds)]
//            );
//        }

        $videoMap = new VideoMap();
        /** @var Google_Service_YouTube_Video $video */
        foreach ($videoIds as $videoId) {
            $this->composeVideoObject($videoId, $videoMap);
        }

        return $videoMap;
    }

    /**
     * @param string   $videoId
     * @param VideoMap $videoMap
     *
     * @return void
     * @throws TooManyRequestsException
     * @throws YouTubeException
     */
    protected function composeVideoObject(string $videoId, VideoMap $videoMap): void
    {
        static $errors = [];

        try {
            $errors[$videoId] = 0;
            $downloadLinks = $this->youTubeDownloader->getDownloadLinks(
                sprintf(self::YOUTUBE_VIDEO_URL, $videoId)
            );
            $directLink = null;
            if ($downloadLinks->getAllFormats()) {
                $videFormats = $downloadLinks->getCombinedFormats();
                /** @var StreamFormat $videoHighQuality */
                $videoHighQuality = array_pop($videFormats);
                $directLink = $videoHighQuality->url ?? null;
            }
            if ($directLink === null) {
                // todo запись в лог
                return;
            }
            $videoMap->offsetSet(
                $videoId,
                new YoutubeVideo($directLink, $downloadLinks->getInfo())
            );
        } catch (YouTubeException $e) {
            $errors[$videoId]++;
            if ($errors[$videoId] < 3) {
                $this->composeVideoObject($videoId, $videoMap);
                return;
            }
        }
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

    /**
     * Список всех ИД, до тех пор пока не закончатся страницы
     * @param array<string, mixed> $params
     * @return array<int, string>
     */
    protected function getVideoIdsWithParams(array $params = []): array
    {
        $listSearch = $this->googleService->search->listSearch(
            'id',
            array_merge($params, $this->searchParams->getForFirstChannel())
        );
        static $videosIds = [];
        /** @var Google_Service_YouTube_SearchResult $item */
        foreach ($listSearch->getItems() as $item) {
            $videosIds[] = $item->getId()->getVideoId();
        }
        if ($listSearch->getNextPageToken()) {
            return $this->getVideoIdsWithParams(['pageToken' => $listSearch->getNextPageToken()]);
        }
        return array_reverse(array_filter($videosIds));
    }
}
