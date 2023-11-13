<?php

declare(strict_types=1);

namespace Coderun\Youtube\ContentAdapter;

use Coderun\Common\ValueObject\Video;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

/**
 * Class Direct
 *
 * @package Coderun\Youtube\ContentAdapter
 */
class Direct implements AdapterInterface
{
    
    /**
     * @param Client $client
     */
    public function __construct(protected Client $client)
    {
    
    }
    
    /**
     * @param Video $video
     *
     * @return string|null
     */
    public function getContent(Video $video): ?string
    {
        $downloadUrl = $this->getUrl($video);
        if (empty($downloadUrl)) {
            return null;
        }
        $request = new Request('GET', $downloadUrl);
        /** @var Response $content */
        $content = $this->client->sendAsync($request)->wait();
        return $content->getBody()->getContents();
    }
    
    /**
     * @param Video $video
     *
     * @return string|null
     */
    public function getUrl(Video $video): ?string
    {
       return $video->getDirectLink();
    }
    
    /**
     * @param Video $video
     *
     * @return string|null
     */
    public function getLocalPath(Video $video): ?string
    {
        return null;
    }
    
}