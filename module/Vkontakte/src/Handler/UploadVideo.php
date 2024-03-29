<?php

declare(strict_types=1);

namespace Coderun\Vkontakte\Handler;

use Coderun\Common\ValueObject\Video;
use Coderun\Vkontakte\Service\UploadVideo as UploadVideoService;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * Class UploadVideo
 *
 * @package Coderun\Vkontakte\Handler
 */
class UploadVideo
{

    /**
     * @param UploadVideoService $service
     */
    public function __construct(protected UploadVideoService $service)
    {
    }

    /**
     * @param Video $video
     *
     * @return array<string,mixed>
     * @throws TransportExceptionInterface
     */
    public function upload(Video $video): array
    {
        $response = $this->service->upload($video);
        $content = json_decode($response->getBody()->getContents(), true);
        return is_array($content) ? $content : [];
    }
}
