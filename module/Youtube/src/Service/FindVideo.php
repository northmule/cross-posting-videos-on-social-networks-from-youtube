<?php

declare(strict_types=1);

namespace Coderun\Youtube\Service;

use Coderun\Common\ValueObject\Video;
use Coderun\Youtube\ValueObject\SearchParams;
use Coderun\Common\ValueObject\Video as YoutubeVideo;
use Coderun\Common\Collection\VideoMap;
use Google\Service\YouTube;
use Google\Service\YouTube\SearchResult;
use YouTube\Exception\TooManyRequestsException;
use YouTube\Exception\YouTubeException;
use YouTube\Models\StreamFormat;
use YouTube\YouTubeDownloader;

use function array_pop;
use function sprintf;
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

    /** @var YouTube  */
    protected YouTube $googleService;
    /** @var SearchParams  */
    protected SearchParams $searchParams;
    /** @var YouTubeDownloader  */
    protected YouTubeDownloader $youTubeDownloader;
    /** @var array<string, int> */
    protected array $errors = [];

    /**
     * @param YouTube $googleService
     * @param SearchParams           $searchParams
     * @param YouTubeDownloader      $youTubeDownloader
     */
    public function __construct(
        YouTube $googleService,
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
        $videoIds = $this->getVideoIds();
        foreach ($videoIds as $videoId) {
            $this->composeVideoObject($videoId, $videoMap);
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
    public function findAll(): VideoMap
    {
        $videoIds = $this->getVideoIdsWithParams();
        $videoMap = new VideoMap();
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
        try {
            $this->errors[$videoId] = $this->errors[$videoId] ?? 0;
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
            $this->errors[$videoId]++;
            if ($this->errors[$videoId] < 3) {
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
        /** @var SearchResult $item */
        foreach ($listSearch->getItems() as $item) {
            $videosIds[] = $item->getId()->getVideoId();
        }
        if ($listSearch->getNextPageToken()) {
            return $this->getVideoIdsWithParams(['pageToken' => $listSearch->getNextPageToken()]);
        }
        return array_reverse(array_filter($videosIds));
    }
}
