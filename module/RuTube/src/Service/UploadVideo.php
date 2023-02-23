<?php

declare(strict_types=1);

namespace Coderun\RuTube\Service;

use Coderun\RuTube\ValueObject\Authorization;
use Coderun\Common\ValueObject\Video;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * Class UploadVideo
 */
class UploadVideo
{
    /** @var string  */
    protected const API_VIDEO_URL = 'https://rutube.ru/api/video/';

    /** @var HttpClientInterface  */
    protected HttpClientInterface $client;

    /**
     * @param HttpClientInterface $client
     */
    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @param Video         $video
     * @param Authorization $authorization
     *
     * @return ResponseInterface
     * @throws TransportExceptionInterface
     */
    public function upload(Video $video, Authorization $authorization): ResponseInterface
    {
        return $this->client->request('POST', self::API_VIDEO_URL, [
            'headers' => [
                'Authorization' => $authorization->getAuthorizationStringForHeader(),
            ],
            'body'    => [
                'url'         => $video->getDirectLink(),
                'title'       => $video->getTitle(),
                'description' => $video->getDescription(),
                'author'      => $authorization->getAuthor(),
            ],
        ]);
    }
}
