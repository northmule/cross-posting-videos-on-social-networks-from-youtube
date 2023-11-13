<?php

declare(strict_types=1);

namespace Coderun\RuTube\Service;

use Coderun\RuTube\ValueObject\Authorization;
use Coderun\Common\ValueObject\Video;
use Coderun\Youtube\ContentAdapter\AdapterInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;


/**
 * Class UploadVideo
 */
class UploadVideo
{
    /** @var string  */
    protected const API_VIDEO_URL = 'https://rutube.ru/api/video/';
   
    /**
     * @param Client  $client
     * @param AdapterInterface $contentAdapter
     */
    public function __construct(protected Client $client, protected AdapterInterface $contentAdapter)
    {
    }

    /**
     * @param Video         $video
     * @param Authorization $authorization
     *
     * @return ResponseInterface
     */
    public function upload(Video $video, Authorization $authorization): ResponseInterface
    {
        $headers = [
            'Authorization' => $authorization->getAuthorizationStringForHeader(),
        ];
        $options = [
            'form_params' => [
                'url'         => $this->contentAdapter->getUrl($video),
                'title'       => $video->getTitle(),
                'description' => $video->getDescription(),
                'author'      => $authorization->getAuthor(),
            ]];
        
        $request = new Request(
            'POST', self::API_VIDEO_URL, $headers
        );
        /** @var Response $res */
        $res = $this->client->sendAsync($request, $options)->wait();
        
        return $res;
    }
}
