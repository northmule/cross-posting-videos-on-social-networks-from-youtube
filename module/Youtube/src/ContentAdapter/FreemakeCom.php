<?php

declare(strict_types=1);

namespace Coderun\Youtube\ContentAdapter;

use Coderun\Common\ValueObject\Video;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

/**
 * Берёт данные через промежуточный сайт https://presaver.com
 *
 * Class FreemakeCom
 *
 * @package Coderun\Youtube\ContentAdapter
 */
class FreemakeCom implements AdapterInterface
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
        /** @var Response $preRequest */
        $preRequest = $this->client->sendAsync(new Request(
            'GET', sprintf('https://downloader.freemake.com/api/videoinfo/%s', $video->getVideoId()), []
        ), [])->wait();
        
        $responseData = json_decode($preRequest->getBody()->getContents(), true);
        $downloadUrl = null;
        foreach ($responseData['qualities'] as $quality) {
            $qualityLabel = $quality['qualityInfo']['qualityLabel'] ?? '';
            if ($qualityLabel === '720p') {
                $downloadUrl = $quality['url'] ?? null;
                break;
            }
        }
        if (empty($downloadUrl)) {
            return null;
        }
        return $downloadUrl;
    }
    
    /**
     * @param Video $video
     *
     * @return string|null
     */
    public function getLocalPath(Video $video): ?string
    {
        $content = $this->getContent($video);
        if ($content === null) {
            return null;
        }
        
        $path = 'data/tmp/'.md5($video->getVideoId()).'.mp4';
        file_put_contents($path, $content);
        return $path;
    }
    
    
}