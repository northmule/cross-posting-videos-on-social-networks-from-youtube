<?php

declare(strict_types=1);

namespace Coderun\Common\ValueObject;

use ReflectionProperty;
use YouTube\Models\VideoDetails;

use function array_pop;
use function count;
use function sprintf;

/**
 * Class Video
 *
 * @package Coderun\Youtube\ValueObject
 */
class Video
{
    /**
     * Оригинальный ИД с Ютуб
     * @var string
     */
    protected string $videoId = '';
    /**
     * Ключевые слова/метки
     *
     * @var array
     */
    protected array $keywords = [];
    /**
     * @var string
     */
    protected string $channelId = '';
    /**
     * Прямая ссылка, для скачивания
     * @var string
     */
    protected string $directLink = '';
    /**
     * Обычная ссылка на видео, браузерная
     * @var string
     */
    protected string $link = '';
    /** @var string */
    protected string $title = '';
    /** @var string */
    protected string $description = '';
    /** @var string  */
    protected string $thumbnailUrl = '';
    
    /**
     * @param string            $directLink
     * @param VideoDetails|null $videoDetails
     *
     * @throws \ReflectionException
     */
    public function __construct(string $directLink, ?VideoDetails $videoDetails = null)
    {
        $videoDetailsProperty = new ReflectionProperty($videoDetails, 'videoDetails');
        /** @var array<string, mixed> $data */
        $data = $videoDetailsProperty->getValue($videoDetails) ?? [];
        $this->videoId = $data['videoId'] ?? '';
        $this->title = $data['title'] ?? '';
        $this->keywords = $data['keywords'] ?? [];
        $this->channelId = $data['channelId'] ?? '';
        $this->description = $data['shortDescription'] ?? '';
        $this->directLink = $directLink;
        $this->thumbnailUrl = $this->fillThumbnailUrl($data['thumbnail']['thumbnails'] ?? []);
        if ($this->videoId) {
            $this->link = sprintf('https://www.youtube.com/watch?v=%s', $this->videoId);
        }
    }

    /**
     * Get videoId
     *
     * @return string
     */
    public function getVideoId(): string
    {
        return $this->videoId;
    }

    /**
     * Get keywords
     *
     * @return array
     */
    public function getKeywords(): array
    {
        return $this->keywords;
    }

    /**
     * Get channelId
     *
     * @return string
     */
    public function getChannelId(): string
    {
        return $this->channelId;
    }

    /**
     * Get directLink
     *
     * @return string
     */
    public function getDirectLink(): string
    {
        return $this->directLink;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Get link
     *
     * @return string
     */
    public function getLink(): string
    {
        return $this->link;
    }

    /**
     * Get thumbnailUrl
     *
     * @return string
     */
    public function getThumbnailUrl(): string
    {
        return $this->thumbnailUrl;
    }

    /**
     * Ссылка на превью картинку
     *
     * @param array $thumbnails
     *
     * @return string
     */
    protected function fillThumbnailUrl(array $thumbnails): string
    {
        if (count($thumbnails) == 0) {
            return '';
        }
        $endThumbnail = array_pop($thumbnails);
        return $endThumbnail['url'] ?? '';
    }
}
