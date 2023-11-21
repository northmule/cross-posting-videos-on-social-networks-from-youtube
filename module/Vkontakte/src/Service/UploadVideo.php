<?php

declare(strict_types=1);

namespace Coderun\Vkontakte\Service;

use Coderun\Vkontakte\ModuleOptions;
use Coderun\Vkontakte\Options\Api;
use Coderun\Vkontakte\ValueObject\Authorization;
use Coderun\Common\ValueObject\Video;
use Coderun\Youtube\ContentAdapter\AdapterInterface;
use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

use function json_decode;
use function intval;

/**
 * Class UploadVideo
 */
class UploadVideo
{
    /** @var string  */
    protected const API_VIDEO_SAVE_URL = 'https://api.vk.com/method/video.save?';
    
    
    /**
     * @param Client  $client
     * @param AdapterInterface $contentAdapter
     */
    public function __construct(protected Client $client, protected AdapterInterface $contentAdapter, protected ModuleOptions $options)
    {
    }

    /**
     * @param Video         $video
     * @param Authorization $authorization
     *
     * @return ResponseInterface
     * @throws TransportExceptionInterface
     */
    public function upload(Video $video): ResponseInterface
    {
        /** @var \GuzzleHttp\Psr7\Response $response */
        $response = $this->client->request(
            'GET',
            self::API_VIDEO_SAVE_URL,
            [
                'query' => [
                    'group_id'     => $this->options->getApi()->getGroupId(),
                    // 'link'         => $video->getLink(),
                    'name'         => $video->getTitle(),
                    'description'  => $video->getDescription(),
                    'wallpost'     => intval($this->options->getApi()->getWallpost()),
                    'access_token' => $this->options->getApi()->getToken(),
                    'album_id'     => $this->options->getApi()->getAlbumId(),
                    'v'            => $this->options->getApi()->getVersion(),
                ],
            ]
        );

        $contentResponse = json_decode($response->getBody()->getContents());
        /** @phpstan-ignore-next-line */
        $uploadUrl = $contentResponse->response->upload_url ?? null;
        if (empty($uploadUrl)) {
            return $response;
        }
        $videoContent = $this->contentAdapter->getContent($video);
        
        if (empty($videoContent)) {
            return new \GuzzleHttp\Psr7\Response();
        }

        /** @var \GuzzleHttp\Psr7\Response $responseVKUpload */
        $responseVKUpload = $this->client->request('POST', $uploadUrl, [
            'multipart' => [
                [
                    'name' => $video->getTitle(),
                    'contents' => $videoContent,
                    'filename' => md5($video->getVideoId()).'.mp4',
                ],
            ],
        ]);
        return $responseVKUpload;
    }

}
