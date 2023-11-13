<?php

declare(strict_types=1);

namespace Coderun\Telegram\Service;

use Coderun\Telegram\ValueObject\Authorization;
use Coderun\Common\ValueObject\Video;
use Coderun\Youtube\ContentAdapter\AdapterInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

use function sprintf;

/**
 * Class UploadVideo
 */
class UploadVideo
{
    /** @var string  */
    protected const API_URL = 'https://api.telegram.org';
    
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
     * @throws TransportExceptionInterface
     */
    public function upload(Video $video, Authorization $authorization): ResponseInterface
    {
        $options = [
            'form_params' => [
                'chat_id' => $authorization->getChanel(),
                'text'    => $video->getLink(),
            ]
        ];
        
        $request = new Request(
            'POST', sprintf('%s/bot%s/%s', self::API_URL, $authorization->getToken(), 'sendMessage')
        );
        /** @var Response $res */
        $res = $this->client->sendAsync($request, $options)->wait();
        
        return $res;
    }
}
