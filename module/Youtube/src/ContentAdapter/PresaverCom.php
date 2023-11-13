<?php

declare(strict_types=1);

namespace Coderun\Youtube\ContentAdapter;

use Coderun\Common\ValueObject\Video;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

/**
 * Class PresaverCom
 *
 * @package Coderun\Youtube\ContentAdapter
 */
class PresaverCom implements AdapterInterface
{
    /** @var array|string[]  */
    protected array $headers = [
        'X-CSRF-TOKEN' => '',
        'Cookie' => '',
    ];
    
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
        $preLoadVideo = $this->preLoadVideoByPresaver($video);
        $downloadUrl = $preLoadVideo['formats'][0]['url'] ?? null;
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
    
    
    /**
     * Предзагрузка видео через сервис presaver.com
     *
     * Создаёт задание на скачивание видео, возращает массив с параметрами и ссылкой на видео
     *
     * @param Video $video
     *
     * @return array{id:string,title:string,formats:array{url:string,format_id:int}}
     */
    protected function preLoadVideoByPresaver(Video $video): array
    {
       /** @var Response $preRequest */
        $preRequest = $this->client->sendAsync(new Request(
            'GET', 'https://presaver.com/', []
        ), [])->wait();
        $content = $preRequest->getBody()->getContents();
        $setCookie = $preRequest->getHeader('set-cookie');
        $presaverSessionValue = $setCookie[1] ?? '';
        $presaverSessionValue = explode(';', $presaverSessionValue);
        $presaverSessionValue = ($presaverSessionValue[0] ?? '');
        preg_match('/\<meta name="csrf-token" content="([a-zA-Z-0-9-_]+)" \/>/', $content, $match);
        $this->setHeaders(['X-CSRF-TOKEN' => $match[1] ?? '-1']);
        $cookie = current($preRequest->getHeader('set-cookie'));
        $cookie = explode(';', $cookie);
        $cookie = 'tz=-180; '.$cookie[0].'; '.$presaverSessionValue;
        $this->setHeaders(['Cookie' => $cookie]);
        $headers = [
            'Content-Type' => 'application/x-www-form-urlencoded',
            'X-Requested-With' => 'XMLHttpRequest',
            'Referer' => sprintf('https://presaver.com/youtube/%s', $video->getVideoId()),
        ];
        $headers = array_merge($this->headers, $headers);
        $options = [
            'form_params' => [
                'vid' => $video->getVideoId()
            ]];
        $request = new Request(
            'POST', 'https://presaver.com/files/info', $headers
        );
        try {
            /** @var Response $res */
            $res = $this->client->sendAsync($request, $options)->wait();
        } catch (\GuzzleHttp\Exception\ClientException $exception) {
            throw $exception; // для точек
        }

        return json_decode($res->getBody()->getContents(), true);
    }
    
    /**
     * @param array $headers
     *
     * @return PresaverCom
     */
    protected function setHeaders(array $headers): PresaverCom
    {
        $this->headers = array_merge($this->headers, $headers);
        return $this;
    }
    
}