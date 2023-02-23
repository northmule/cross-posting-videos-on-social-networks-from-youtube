<?php

declare(strict_types=1);

namespace Coderun\Vkontakte\Service;

use Coderun\Vkontakte\ValueObject\Authorization;
use Coderun\Common\ValueObject\Video;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

use function json_decode;

/**
 * Class UploadVideo
 */
class UploadVideo
{
    /** @var string  */
    protected const API_VIDEO_SAVE_URL = 'https://api.vk.com/method/video.save?';

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
        $response = $this->client->request(
            'GET',
            self::API_VIDEO_SAVE_URL,
            [
                'query' => [
                    'group_id'     => $authorization->getGroupId(),
                    'link'         => $video->getLink(),
                    'name'         => $video->getTitle(),
                    'description'  => $video->getDescription(),
                    'wallpost'     => 1,
                    'access_token' => $authorization->getToken(),
                    'album_id'     => $authorization->getAlbumId(),
                    'v'            => $authorization->getApiVersion(),
                ],
            ]
        );

        $contentResponse = json_decode($response->getContent());
        $uploadUrl = $contentResponse->response->upload_url ?? null;
        if (empty($uploadUrl)) {
            return $response;
        }
        return $this->client->request('GET', $uploadUrl);
    }
}
