<?php

declare(strict_types=1);

namespace Coderun\Telegram\Service;

use Coderun\Telegram\ValueObject\Authorization;
use Coderun\Common\ValueObject\Video;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

use function sprintf;

/**
 * Class UploadVideo
 */
class UploadVideo
{
    /** @var string  */
    protected const API_URL = 'https://api.telegram.org';
    
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
        return $this->client->request('POST',
            sprintf('%s/bot%s/%s', self::API_URL, $authorization->getToken(), 'sendMessage'),
            [
                'body' => [
                    'chat_id' => $authorization->getChanel(),
                    'text' => $video->getLink(),
                ]
            ]
        );
    }
}